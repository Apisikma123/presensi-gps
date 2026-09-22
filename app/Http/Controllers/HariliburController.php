<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Detailharilibur;
use App\Models\Harilibur;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HariliburController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data['user'] = $user;
        $query = Harilibur::query();
        $query->leftJoin('cabang', 'hari_libur.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'hari_libur.*',
                DB::raw("CASE WHEN hari_libur.kode_cabang = 'ALL' THEN 'SEMUA CABANG (NASIONAL)' ELSE COALESCE(cabang.nama_cabang, hari_libur.kode_cabang) END as nama_cabang")
            );

        // Filter berdasarkan akses cabang jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            if (!empty($userCabangs)) {
                $query->where(function ($q) use ($userCabangs) {
                    $q->whereIn('hari_libur.kode_cabang', $userCabangs)
                      ->orWhere('hari_libur.kode_cabang', 'ALL');
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($request->kode_cabang)) {
            $query->where('hari_libur.kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }
        $query->orderBy('hari_libur.tanggal', 'desc');
        $harilibur = $query->paginate(15);
        $harilibur->appends($request->all());
        $data['harilibur'] = $harilibur;

        $data['cabang'] = $user->getCabang();

        return view('harilibur.index', $data);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data['cabang'] = $user->getCabang();
        $data['user'] = $user;
        return view('harilibur.create', $data);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = User::findOrFail(auth()->user()->id);
        $validationRules = [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'kode_cabang' => 'required|string',
        ];

        $request->validate($validationRules);

        try {
            $tahun = date('y', strtotime($request->tanggal));
            $kode_cabang = $request->kode_cabang;

            if ($kode_cabang === 'ALL') {
                $branches = Cabang::all();
            } else {
                if (!$user->isSuperAdmin()) {
                    $userCabangs = $user->getCabangCodes();
                    if (!in_array($kode_cabang, $userCabangs)) {
                        $kode_cabang = $userCabangs[0] ?? $user->kode_cabang;
                    }
                }
                $branches = Cabang::where('kode_cabang', $kode_cabang)->get();
            }

            foreach ($branches as $branch) {
                // Check if holiday already exists for this branch and date
                $exists = Harilibur::where('tanggal', $request->tanggal)
                    ->where('kode_cabang', $branch->kode_cabang)
                    ->first();

                if ($exists) {
                    $kode_libur = $exists->kode_libur;
                    $exists->update(['keterangan' => $request->keterangan]);
                } else {
                    $lastharilibur = Harilibur::select('kode_libur')
                        ->whereRaw('MID(kode_libur, 3, 2) = ?', [$tahun])
                        ->orderBy('kode_libur', 'desc')
                        ->first();

                    $last_kode_libur = $lastharilibur ? $lastharilibur->kode_libur : '';
                    $kode_libur = buatkode($last_kode_libur, "LB" . $tahun, 3);

                    Harilibur::create([
                        'kode_libur' => $kode_libur,
                        'tanggal' => $request->tanggal,
                        'kode_cabang' => $branch->kode_cabang,
                        'keterangan' => $request->keterangan,
                    ]);
                }

                // Auto assign karyawan jika opsi dicentang
                if ($request->has('auto_assign_karyawan') && $request->auto_assign_karyawan == '1') {
                    $karyawans = Karyawan::where('kode_cabang', $branch->kode_cabang)->select('nik')->get();
                    $now = now();
                    foreach ($karyawans as $k) {
                        Detailharilibur::firstOrCreate(
                            ['kode_libur' => $kode_libur, 'nik' => $k->nik],
                            ['created_at' => $now, 'updated_at' => $now]
                        );
                    }
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Hari Libur Berhasil Ditambahkan'
                ]);
            }

            return Redirect::back()->with(messageSuccess('Data Hari Libur Berhasil Ditambahkan'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function edit($kode_libur)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $kode_libur = Crypt::decrypt($kode_libur);
        $data['harilibur'] = Harilibur::where('kode_libur', $kode_libur)->firstOrFail();
        $data['cabang'] = $user->getCabang();
        $data['user'] = $user;
        return view('harilibur.edit', $data);
    }

    public function update(Request $request, $kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $validationRules = [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'kode_cabang' => 'required|string',
        ];

        $request->validate($validationRules);

        try {
            Harilibur::where('kode_libur', $kode_libur)->update([
                'tanggal' => $request->tanggal,
                'kode_cabang' => $request->kode_cabang,
                'keterangan' => $request->keterangan,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Hari Libur Berhasil Diperbarui'
                ]);
            }

            return Redirect::back()->with(messageSuccess('Data Hari Libur Berhasil Diperbarui'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        try {
            Detailharilibur::where('kode_libur', $kode_libur)->delete();
            Harilibur::where('kode_libur', $kode_libur)->delete();
            return Redirect::back()->with(messageSuccess('Data Hari Libur Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function aturharilibur($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $data['harilibur'] = Harilibur::where('kode_libur', $kode_libur)
            ->leftJoin('cabang', 'hari_libur.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'hari_libur.*',
                DB::raw("CASE WHEN hari_libur.kode_cabang = 'ALL' THEN 'SEMUA CABANG (NASIONAL)' ELSE COALESCE(cabang.nama_cabang, hari_libur.kode_cabang) END as nama_cabang")
            )
            ->firstOrFail();
        return view('harilibur.aturharilibur', $data);
    }

    public function aturkaryawan($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $harilibur = Harilibur::where('kode_libur', $kode_libur)
            ->leftJoin('cabang', 'hari_libur.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'hari_libur.*',
                DB::raw("CASE WHEN hari_libur.kode_cabang = 'ALL' THEN 'SEMUA CABANG (NASIONAL)' ELSE COALESCE(cabang.nama_cabang, hari_libur.kode_cabang) END as nama_cabang")
            )
            ->firstOrFail();
        $data['departemen'] = Departemen::orderBy('kode_dept')->get();
        $data['harilibur'] = $harilibur;

        return view('harilibur.aturkaryawan', $data);
    }

    public function getkaryawan(Request $request)
    {
        $kode_libur = Crypt::decrypt($request->kode_libur);
        $harilibur = Harilibur::where('kode_libur', $kode_libur)
            ->leftJoin('cabang', 'hari_libur.kode_cabang', '=', 'cabang.kode_cabang')
            ->firstOrFail();
        $data['harilibur'] = $harilibur;

        $query = Karyawan::query();
        $query->select('karyawan.nik', 'karyawan.nik_show', 'karyawan.nama_karyawan', 'harilibur.nik as ceklibur', 'departemen.nama_dept');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        if ($harilibur->kode_cabang !== 'ALL') {
            $query->where('karyawan.kode_cabang', $harilibur->kode_cabang);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        $query->leftJoin(
            DB::raw("(
                SELECT nik FROM hari_libur_detail
                WHERE kode_libur = " . DB::getPdo()->quote($kode_libur) . "
            ) harilibur"),
            function ($join) {
                $join->on('karyawan.nik', '=', 'harilibur.nik');
            }
        );
        $query->orderBy('nama_karyawan');
        $data['karyawan'] = $query->get();
        return view('harilibur.getkaryawan', $data);
    }

    public function updateliburkaryawan(Request $request)
    {
        try {
            $cek = Detailharilibur::where('nik', $request->nik)->where('kode_libur', $request->kode_libur)->first();
            if ($cek != null) {
                Detailharilibur::where('nik', $request->nik)->where('kode_libur', $request->kode_libur)->delete();
            } else {
                Detailharilibur::create([
                    'nik' => $request->nik,
                    'kode_libur' => $request->kode_libur,
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Update Success']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getkaryawanlibur($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $data['detailharilibur'] = Detailharilibur::join('karyawan', 'hari_libur_detail.nik', '=', 'karyawan.nik')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('hari_libur_detail.*', 'karyawan.nik', 'karyawan.nik_show', 'karyawan.nama_karyawan', 'karyawan.kode_dept', 'departemen.nama_dept')
            ->where('kode_libur', $kode_libur)
            ->orderBy('karyawan.nama_karyawan')
            ->get();
        return view('harilibur.getkaryawanlibur', $data);
    }

    public function deletekaryawanlibur(Request $request)
    {
        try {
            Detailharilibur::where('nik', $request->nik)->where('kode_libur', $request->kode_libur)->delete();
            return response()->json(['success' => true, 'message' => 'Delete Success']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tambahkansemua(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $harilibur = Harilibur::where('kode_libur', $kode_libur)->firstOrFail();

        $query = Karyawan::query();
        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        if ($harilibur->kode_cabang !== 'ALL') {
            $query->where('karyawan.kode_cabang', $harilibur->kode_cabang);
        }
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        $karyawan = $query->select('karyawan.nik')->get();

        try {
            $now = now();
            foreach ($karyawan as $d) {
                Detailharilibur::firstOrCreate(
                    ['kode_libur' => $kode_libur, 'nik' => $d->nik],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }

            return response()->json(['success' => true, 'message' => 'Semua karyawan berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function batalkansemua(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $harilibur = Harilibur::where('kode_libur', $kode_libur)->firstOrFail();

        $query = Karyawan::query();
        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        if ($harilibur->kode_cabang !== 'ALL') {
            $query->where('karyawan.kode_cabang', $harilibur->kode_cabang);
        }
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        $niks = $query->pluck('nik');

        try {
            Detailharilibur::where('kode_libur', $kode_libur)->whereIn('nik', $niks)->delete();

            return response()->json(['success' => true, 'message' => 'Semua karyawan berhasil dibatalkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
