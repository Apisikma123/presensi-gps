<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Facerecognition;
use App\Models\Jabatan;
use App\Models\Jamkerja;
use App\Models\Setjamkerjabyday;
use App\Models\Setjamkerjabydate;
use App\Models\Karyawan;
use App\Models\Division;
use App\Models\User;
use App\Models\Userkaryawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Imports\KaryawanImport;
use App\Exports\TemplateKaryawanExport;
use App\Exports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = Karyawan::query();

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        if (!empty($request->kode_jabatan)) {
            $query->where('karyawan.kode_jabatan', $request->kode_jabatan);
        }
        if (!empty($request->kode_jam_kerja)) {
            $query->where('karyawan.kode_jam_kerja', $request->kode_jam_kerja);
        }
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        $sortBy = $request->get('sort_by', 'nama_karyawan');
        if ($sortBy === 'nik_show') {
            $query->orderBy('karyawan.nik_show', 'asc');
        } else {
            $query->orderBy('karyawan.nama_karyawan', 'asc');
        }

        $perPage = (int) $request->get('per_page', 10);
        $karyawanPaginator = $query->paginate($perPage);

        if ($karyawanPaginator->isNotEmpty()) {
            $nikList = $karyawanPaginator->pluck('nik')->toArray();
            $details = Karyawan::whereIn('karyawan.nik', $nikList)
                ->select(
                    'karyawan.nik',
                    'karyawan.nik_show',
                    'karyawan.nama_karyawan',
                    'karyawan.foto',
                    'karyawan.no_hp',
                    'karyawan.email',
                    'karyawan.alamat',
                    'karyawan.jenis_kelamin',
                    'karyawan.status_karyawan',
                    'karyawan.status_aktif_karyawan',
                    'karyawan.tanggal_masuk',
                    'karyawan.tanggal_nonaktif',
                    'karyawan.lock_location',
                    'karyawan.lock_jam_kerja',
                    'karyawan.kode_dept',
                    'karyawan.kode_cabang',
                    'karyawan.kode_jabatan',
                    'karyawan.kode_jam_kerja',
                    'departemen.nama_dept',
                    'jabatan.nama_jabatan',
                    'cabang.nama_cabang',
                    'presensi_jamkerja.nama_jam_kerja',
                    'users_karyawan.id_user'
                )
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin('presensi_jamkerja', 'karyawan.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->leftJoin('users_karyawan', 'karyawan.nik', '=', 'users_karyawan.nik')
                ->get()
                ->keyBy('nik');

            $ordered = collect($nikList)->map(fn($nik) => $details->get($nik))->filter();
            $karyawan = $karyawanPaginator->setCollection($ordered);
        } else {
            $karyawan = $karyawanPaginator;
        }

        $karyawan->appends($request->all());

        $data['karyawan'] = $karyawan;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['jabatan'] = Cache::remember('all_jabatan_list', 3600, fn() => Jabatan::select('kode_jabatan', 'nama_jabatan')->orderBy('nama_jabatan')->get());
        $data['jamkerja'] = Cache::remember('all_jamkerja_list', 3600, fn() => Jamkerja::select('kode_jam_kerja', 'nama_jam_kerja')->orderBy('kode_jam_kerja')->get());

        return view('datamaster.karyawan.index', $data);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['jabatan'] = Jabatan::orderBy('kode_jabatan')->get();
        $data['jamkerja'] = Jamkerja::orderBy('kode_jam_kerja')->get();
        $data['divisi'] = Division::where('is_active', true)->orderBy('nama_divisi')->get();
        $data['supervisors'] = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get(['nik', 'nama_karyawan', 'nik_show']);
        return view('datamaster.karyawan.create', $data);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'nama_karyawan' => 'required',
            'jenis_kelamin' => 'required',
            'kode_cabang' => 'required',
            'kode_dept' => 'required',
            'kode_jabatan' => 'required',
            'kode_jam_kerja' => 'required',
            'tanggal_masuk' => 'required',
            'nik_show' => 'nullable',
            'no_hp' => 'nullable',
            'alamat' => 'nullable',
            'password' => 'nullable|min:3',
            'rfid_uid' => 'nullable',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($request->kode_cabang, $userCabangs)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke cabang yang dipilih'], 403);
                }
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke cabang yang dipilih'));
            }

            if (!in_array($request->kode_dept, $userDepartemens)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke departemen yang dipilih'], 403);
                }
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke departemen yang dipilih'));
            }
        }

        // Prevent rapid duplicate submission (concurrency / double submit / duplicate AJAX)
        $recentDuplicate = Karyawan::where('nama_karyawan', $request->nama_karyawan)
            ->where('kode_cabang', $request->kode_cabang)
            ->where('kode_dept', $request->kode_dept)
            ->where('tanggal_masuk', $request->tanggal_masuk)
            ->where('created_at', '>=', now()->subSeconds(5))
            ->first();

        if ($recentDuplicate) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Karyawan Berhasil Disimpan'
                ]);
            }
            return Redirect::back()->with(messageSuccess('Data Karyawan Berhasil Disimpan'));
        }

        DB::beginTransaction();
        try {
            $tahun = date('y');
            $bulan = date('m');
            $prefix = $tahun . $bulan;

            $last = Karyawan::where('nik', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('nik', 'desc')
                ->first();

            $lastNumber = 0;
            if ($last) {
                $lastNumber = (int)substr($last->nik, 4, 5);
            }
            $nextNumber = $lastNumber + 1;
            $nikAuto = $prefix . str_pad((string)$nextNumber, 5, '0', STR_PAD_LEFT);

            $data_foto = [];
            if ($request->hasfile('foto')) {
                $foto_name = \App\Helpers\ImageOptimizer::saveAsWebp(
                    $request->file('foto'),
                    'karyawan',
                    $nikAuto . "_" . time(),
                    80,
                    800
                );
                $data_foto = ['foto' => $foto_name];
            }

            $isRandomPassword = !$request->filled('password');
            $plainPassword = $isRandomPassword ? \Illuminate\Support\Str::password(10, numbers: true, symbols: false) : $request->password;
            $hashedPassword = Hash::make($plainPassword);
            $nikShow = !empty($request->nik_show) ? $request->nik_show : $nikAuto;

            $data_karyawan = [
                'nik' => $nikAuto,
                'nik_show' => $nikShow,
                'nama_karyawan' => $request->nama_karyawan,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
                'personal_email' => $request->personal_email,
                'company_email' => $request->company_email,
                'kontak_darurat' => $request->kontak_darurat,
                'hubungan_kontak_darurat' => $request->hubungan_kontak_darurat,
                'no_ktp' => $request->no_ktp,
                'npwp_number' => $request->npwp_number,
                'bpjs_kesehatan_number' => $request->bpjs_kesehatan_number,
                'bpjs_ketenagakerjaan_number' => $request->bpjs_ketenagakerjaan_number,
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'nama_rekening' => $request->nama_rekening,
                'nationality' => $request->nationality ?: 'WNI',
                'religion' => $request->religion,
                'grade_level' => $request->grade_level,
                'direct_supervisor_nik' => $request->direct_supervisor_nik,
                'employment_type' => $request->employment_type ?: 'PKWT',
                'kode_divisi' => $request->kode_divisi,
                'kode_cabang' => $request->kode_cabang,
                'kode_dept' => $request->kode_dept,
                'kode_jabatan' => $request->kode_jabatan,
                'kode_jam_kerja' => $request->kode_jam_kerja ?: 'JK01',
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_aktif_karyawan' => '1',
                'lock_location' => 1,
                'lock_jam_kerja' => 1,
                'rfid_uid' => $request->rfid_uid,
                'pin' => $request->pin
            ];

            $data = array_merge($data_karyawan, $data_foto);
            $simpan = Karyawan::create($data);

            if ($simpan) {
                $userCreated = User::create([
                    'name' => $request->nama_karyawan,
                    'username' => $nikAuto,
                    'email' => $request->email ?: ($nikAuto . '@gmail.com'),
                    'password' => $hashedPassword,
                ]);
                $userCreated->assignRole('karyawan');
                Userkaryawan::create([
                    'id_user' => $userCreated->id,
                    'nik' => $nikAuto
                ]);
            }

            DB::commit();
            $successMsg = $isRandomPassword
                ? "Data Karyawan Berhasil Disimpan (Password sementara: {$plainPassword})"
                : 'Data Karyawan Berhasil Disimpan';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'temp_password' => $isRandomPassword ? $plainPassword : null,
                    'username' => $nikAuto,
                    'nama_karyawan' => $request->nama_karyawan,
                ]);
            }
            return Redirect::back()->with(messageSuccess($successMsg));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Check if authenticated user is authorized to manage the given karyawan.
     */
    protected function authorizeKaryawanAccess($user, $karyawan): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        $userCabangs = $user->getCabangCodes();
        $userDepartemens = $user->getDepartemenCodes();
        return in_array($karyawan->kode_cabang, $userCabangs) && in_array($karyawan->kode_dept, $userDepartemens);
    }

    public function edit($nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            abort(404, 'Karyawan tidak ditemukan');
        }

        if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan cabang ini.');
        }

        $data['karyawan'] = $karyawan;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['jabatan'] = Jabatan::orderBy('kode_jabatan')->get();
        $data['jamkerja'] = Jamkerja::orderBy('kode_jam_kerja')->get();
        $data['divisi'] = Division::where('is_active', true)->orderBy('nama_divisi')->get();
        $data['supervisors'] = Karyawan::where('status_aktif_karyawan', '1')->where('nik', '!=', $nik)->orderBy('nama_karyawan')->get(['nik', 'nama_karyawan', 'nik_show']);
        return view('datamaster.karyawan.edit', $data);
    }

    public function update($nik, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            abort(404, 'Karyawan tidak ditemukan');
        }

        $request->validate([
            'nama_karyawan' => 'required',
            'jenis_kelamin' => 'required',
            'kode_cabang' => 'required',
            'kode_dept' => 'required',
            'kode_jabatan' => 'required',
            'kode_jam_kerja' => 'required',
            'tanggal_masuk' => 'required',
            'nik_show' => 'nullable',
            'no_hp' => 'nullable',
            'alamat' => 'nullable',
            'status_aktif_karyawan' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if (!$user->isSuperAdmin()) {
            if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke data karyawan cabang ini'], 403);
                }
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke data karyawan cabang ini'));
            }

            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($request->kode_cabang, $userCabangs)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke cabang yang dipilih'], 403);
                }
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke cabang yang dipilih'));
            }

            if (!in_array($request->kode_dept, $userDepartemens)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke departemen yang dipilih'], 403);
                }
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke departemen yang dipilih'));
            }
        }

        try {
            $data_foto = [];
            if ($request->hasfile('foto')) {
                if (!empty($karyawan->foto) && Storage::disk('public')->exists('karyawan/' . $karyawan->foto)) {
                    Storage::disk('public')->delete('karyawan/' . $karyawan->foto);
                }

                $foto_name = \App\Helpers\ImageOptimizer::saveAsWebp(
                    $request->file('foto'),
                    'karyawan',
                    $nik . "_" . time(),
                    80,
                    800
                );
                $data_foto = ['foto' => $foto_name];
            }

            $data_karyawan = [
                'nik_show' => $request->nik_show ?: $nik,
                'nama_karyawan' => $request->nama_karyawan,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
                'personal_email' => $request->personal_email,
                'company_email' => $request->company_email,
                'kontak_darurat' => $request->kontak_darurat,
                'hubungan_kontak_darurat' => $request->hubungan_kontak_darurat,
                'no_ktp' => $request->no_ktp,
                'npwp_number' => $request->npwp_number,
                'bpjs_kesehatan_number' => $request->bpjs_kesehatan_number,
                'bpjs_ketenagakerjaan_number' => $request->bpjs_ketenagakerjaan_number,
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'nama_rekening' => $request->nama_rekening,
                'nationality' => $request->nationality ?: 'WNI',
                'religion' => $request->religion,
                'grade_level' => $request->grade_level,
                'direct_supervisor_nik' => $request->direct_supervisor_nik,
                'employment_type' => $request->employment_type ?: 'PKWT',
                'kode_divisi' => $request->kode_divisi,
                'kode_cabang' => $request->kode_cabang,
                'kode_dept' => $request->kode_dept,
                'kode_jabatan' => $request->kode_jabatan,
                'kode_jam_kerja' => $request->kode_jam_kerja ?: 'JK01',
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_aktif_karyawan' => $request->status_aktif_karyawan,
                'rfid_uid' => $request->rfid_uid,
                'pin' => $request->pin
            ];

            $data = array_merge($data_karyawan, $data_foto);
            Karyawan::where('nik', $nik)->update($data);

            $user_karyawan = Userkaryawan::where('nik', $nik)->first();
            if ($user_karyawan) {
                $userData = ['name' => $request->nama_karyawan];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                    $targetUser = User::find($user_karyawan->id_user);
                    if ($targetUser && method_exists($targetUser, 'tokens')) {
                        $targetUser->tokens()->delete();
                    }
                }
                User::where('id', $user_karyawan->id_user)->update($userData);

                if ($request->status_aktif_karyawan == 0) {
                    \Illuminate\Support\Facades\Cache::forget('user_karyawan_valid_' . $user_karyawan->id_user);
                    $targetUser = User::find($user_karyawan->id_user);
                    if ($targetUser && method_exists($targetUser, 'tokens')) {
                        $targetUser->tokens()->delete();
                    }
                }
            } else if ($request->filled('password')) {
                $newUser = User::create([
                    'name' => $request->nama_karyawan,
                    'username' => $nik,
                    'email' => $nik . '@gmail.com',
                    'password' => Hash::make($request->password),
                ]);
                $newUser->assignRole('karyawan');
                Userkaryawan::create([
                    'id_user' => $newUser->id,
                    'nik' => $nik
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Karyawan Berhasil Disimpan'
                ]);
            }
            return Redirect::back()->with(messageSuccess('Data Karyawan Berhasil Disimpan'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('karyawan.nik', $nik)
            ->select(
                'karyawan.*',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                'jabatan.nama_jabatan',
                'presensi_jamkerja.nama_jam_kerja',
                'divisions.nama_divisi',
                'spv.nama_karyawan as nama_supervisor'
            )
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('presensi_jamkerja', 'karyawan.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->leftJoin('divisions', 'karyawan.kode_divisi', '=', 'divisions.kode_divisi')
            ->leftJoin('karyawan as spv', 'karyawan.direct_supervisor_nik', '=', 'spv.nik')
            ->first();

        if (!$karyawan) {
            abort(404, 'Karyawan tidak ditemukan');
        }

        if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan cabang ini.');
        }

        $user_karyawan = Userkaryawan::where('nik', $nik)->first();
        $targetUser = $user_karyawan ? User::where('id', $user_karyawan->id_user)->first() : null;
        $karyawan_wajah = Facerecognition::where('nik', $nik)->get();

        $data['karyawan'] = $karyawan;
        $data['user'] = $targetUser;
        $data['karyawan_wajah'] = $karyawan_wajah;
        $data['kontraks'] = $karyawan->kontraks;
        $data['activeKontrak'] = $karyawan->activeKontrak;
        $data['movements'] = $karyawan->movements()->with('approver')->get();
        $data['resignations'] = $karyawan->resignations;
        return view('datamaster.karyawan.show', $data);
    }

    public function destroy($nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if (!$karyawan) {
                return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
            }

            if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses untuk menghapus karyawan cabang ini'));
            }

            // Guard against destructive cascade deletion of attendance or payroll history
            $hasAttendance = \App\Models\Presensi::where('nik', $nik)->exists();
            $hasPayroll = \App\Models\PayrollDetail::where('nik', $nik)->exists();
            if ($hasAttendance || $hasPayroll) {
                return Redirect::back()->with(messageError('Karyawan tidak dapat dihapus karena memiliki riwayat kehadiran atau payroll historis. Silakan nonaktifkan status karyawan atau proses pengunduran diri (resign) untuk menjaga integritas data.'));
            }

            $user_karyawan = Userkaryawan::where('nik', $nik)->first();
            if (!empty($user_karyawan)) {
                User::where('id', $user_karyawan->id_user)->delete();
                Userkaryawan::where('nik', $nik)->delete();
            }

            $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
            $path_folder = 'public/uploads/facerecognition/' . $nama_folder;
            Storage::deleteDirectory($path_folder);

            $nama_file_foto = $karyawan->foto;
            $path_foto = '/public/karyawan/' . $nama_file_foto;
            Storage::delete($path_foto);
            Karyawan::where('nik', $nik)->delete();
            return Redirect::back()->with(messageSuccess('Data Karyawan Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function lockunlocklocation(Request $request, $nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if (!$karyawan) {
                abort(404, 'Karyawan tidak ditemukan');
            }

            if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
                abort(403, 'Anda tidak memiliki akses ke karyawan cabang ini.');
            }

            $lock_location = $karyawan->lock_location == '1' ? 0 : 1;
            Karyawan::where('nik', $nik)->update(['lock_location' => $lock_location]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $lock_location,
                    'message' => $lock_location == 1 ? 'Lokasi GPS Karyawan Terkunci' : 'Lokasi GPS Karyawan Bebas'
                ]);
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function lockunlockjamkerja(Request $request, $nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if (!$karyawan) {
                abort(404, 'Karyawan tidak ditemukan');
            }

            if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
                abort(403, 'Anda tidak memiliki akses ke karyawan cabang ini.');
            }

            $lock_jam_kerja = $karyawan->lock_jam_kerja == '1' ? 0 : 1;
            Karyawan::where('nik', $nik)->update(['lock_jam_kerja' => $lock_jam_kerja]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $lock_jam_kerja,
                    'message' => $lock_jam_kerja == 1 ? 'Shift Jam Kerja Terkunci' : 'Shift Jam Kerja Bebas'
                ]);
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function getkaryawan(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->can('karyawan.index') && !$user->can('laporan.presensi')) {
            abort(403, 'Unauthorized access.');
        }

        $query = Karyawan::query()->select('nik', 'nik_show', 'nama_karyawan');

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            $query->whereIn('kode_cabang', !empty($userCabangs) ? $userCabangs : ['INVALID']);
            $query->whereIn('kode_dept', !empty($userDepartemens) ? $userDepartemens : ['INVALID']);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        $karyawan = $query->orderBy('nama_karyawan')->get();
        return response()->json($karyawan);
    }

    public function getkaryawantable(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->can('karyawan.index')) {
            abort(403, 'Unauthorized access.');
        }

        $query = Karyawan::query();

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            $query->whereIn('kode_cabang', !empty($userCabangs) ? $userCabangs : ['INVALID']);
            $query->whereIn('kode_dept', !empty($userDepartemens) ? $userDepartemens : ['INVALID']);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        if (!empty($request->q)) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_karyawan', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%");
            });
        }
        $data['karyawan'] = $query->orderBy('nama_karyawan')->get();
        return view('datamaster.karyawan.getkaryawantable', $data);
    }

    public function createuser($nik)
    {
        /** @var \App\Models\User $userAuth */
        $userAuth = auth()->user();

        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
        }

        if (!$this->authorizeKaryawanAccess($userAuth, $karyawan)) {
            return Redirect::back()->with(messageError('Anda tidak memiliki akses untuk membuat user karyawan cabang ini'));
        }

        try {
            $tempPassword = \Illuminate\Support\Str::password(10, numbers: true, symbols: false);
            $user = User::create([
                'name' => $karyawan->nama_karyawan,
                'username' => $karyawan->nik,
                'email' => $karyawan->nik . '@gmail.com',
                'password' => Hash::make($tempPassword),
            ]);
            $user->assignRole('karyawan');
            Userkaryawan::create([
                'id_user' => $user->id,
                'nik' => $karyawan->nik
            ]);
            return Redirect::back()->with(messageSuccess('User Berhasil Dibuat (Password sementara: ' . $tempPassword . ')'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function deleteuser($nik)
    {
        /** @var \App\Models\User $userAuth */
        $userAuth = auth()->user();

        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if ($karyawan && !$this->authorizeKaryawanAccess($userAuth, $karyawan)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses untuk menghapus user karyawan cabang ini'));
            }

            $user_karyawan = Userkaryawan::where('nik', $nik)->first();
            if ($user_karyawan) {
                User::where('id', $user_karyawan->id_user)->delete();
                Userkaryawan::where('nik', $nik)->delete();
            }
            return Redirect::back()->with(messageSuccess('User Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new KaryawanExport($request->all()), 'Data_Karyawan.xlsx');
    }

    public function download_template()
    {
        return Excel::download(new TemplateKaryawanExport, 'Template_Import_Karyawan.xlsx');
    }

    public function import()
    {
        return view('datamaster.karyawan.import_modal');
    }

    public function import_proses(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,xls,xlsx']);
        try {
            Excel::import(new KaryawanImport, $request->file('file'));
            return Redirect::back()->with(messageSuccess('Data Karyawan Berhasil Diimport'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function setjamkerja($nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('karyawan.*', 'departemen.nama_dept', 'cabang.nama_cabang')
            ->firstOrFail();

        if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengatur jadwal karyawan cabang ini.');
        }

        $data['karyawan'] = $karyawan;
        $data['list_bulan'] = [
            ['kode_bulan' => 1, 'nama_bulan' => 'Januari'],
            ['kode_bulan' => 2, 'nama_bulan' => 'Februari'],
            ['kode_bulan' => 3, 'nama_bulan' => 'Maret'],
            ['kode_bulan' => 4, 'nama_bulan' => 'April'],
            ['kode_bulan' => 5, 'nama_bulan' => 'Mei'],
            ['kode_bulan' => 6, 'nama_bulan' => 'Juni'],
            ['kode_bulan' => 7, 'nama_bulan' => 'Juli'],
            ['kode_bulan' => 8, 'nama_bulan' => 'Agustus'],
            ['kode_bulan' => 9, 'nama_bulan' => 'September'],
            ['kode_bulan' => 10, 'nama_bulan' => 'Oktober'],
            ['kode_bulan' => 11, 'nama_bulan' => 'November'],
            ['kode_bulan' => 12, 'nama_bulan' => 'Desember'],
        ];
        $data['start_year'] = 2024;
        $data['jamkerja'] = Jamkerja::orderBy('kode_jam_kerja')->get();
        $data['cabang'] = $user ? $user->getCabang() : Cabang::all();
        $data['jamkerjabyday'] = Setjamkerjabyday::where('nik', $nik)->get()->keyBy('hari');
        return view('datamaster.karyawan.setjamkerja', $data);
    }

    public function storejamkerjabyday(Request $request, $nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->firstOrFail();
        if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengatur jadwal karyawan cabang ini.');
        }

        $hari = $request->hari ?? [];
        $kode_jam_kerja = $request->kode_jam_kerja ?? [];
        $kode_cabang = $request->kode_cabang ?? [];
        DB::beginTransaction();
        try {
            Setjamkerjabyday::where('nik', $nik)->delete();
            for ($i = 0; $i < count($hari); $i++) {
                if (!empty($kode_jam_kerja[$i])) {
                    Setjamkerjabyday::create([
                        'nik' => $nik,
                        'hari' => $hari[$i],
                        'kode_jam_kerja' => $kode_jam_kerja[$i],
                        'kode_cabang' => !empty($kode_cabang[$i]) ? $kode_cabang[$i] : null
                    ]);
                }
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Jadwal Mingguan Karyawan Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function storejamkerjabydate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $nik = $request->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }
        if (!$this->authorizeKaryawanAccess($user, $karyawan)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengatur jadwal karyawan cabang ini.'], 403);
        }

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        try {
            Setjamkerjabydate::updateOrCreate(
                ['nik' => $request->nik, 'tanggal' => $tanggal],
                [
                    'kode_jam_kerja' => $request->kode_jam_kerja,
                    'kode_cabang' => !empty($request->kode_cabang) ? $request->kode_cabang : null
                ]
            );
            return response()->json(['success' => true, 'message' => 'Jadwal Tanggal Berhasil Disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getjamkerjabydate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $nik = $request->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        if ($karyawan && !$this->authorizeKaryawanAccess($user, $karyawan)) {
            return response()->json([], 403);
        }

        $bulan = (int)$request->bulan;
        $tahun = (int)$request->tahun;

        $jamkerjabydate = Setjamkerjabydate::where('nik', $nik)
            ->join('presensi_jamkerja', 'presensi_jamkerja.kode_jam_kerja', '=', 'presensi_jamkerja_bydate.kode_jam_kerja')
            ->leftJoin('cabang', 'cabang.kode_cabang', '=', 'presensi_jamkerja_bydate.kode_cabang')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->select('presensi_jamkerja_bydate.*', 'presensi_jamkerja.nama_jam_kerja', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang', 'presensi_jamkerja.color', 'cabang.nama_cabang')
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json($jamkerjabydate);
    }

    public function deletejamkerjabydate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $nik = $request->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        if ($karyawan && !$this->authorizeKaryawanAccess($user, $karyawan)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk menghapus jadwal karyawan cabang ini.'], 403);
        }

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        try {
            Setjamkerjabydate::where('nik', $request->nik)->where('tanggal', $tanggal)->delete();
            return response()->json(['success' => true, 'message' => 'Jadwal Tanggal Berhasil Dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
