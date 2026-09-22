<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PresensiDispensasi;
use App\Models\Userkaryawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DispensasiController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = PresensiDispensasi::query()
            ->join('karyawan', 'presensi_dispensasi.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->select(
                'presensi_dispensasi.*',
                'karyawan.nama_karyawan',
                'karyawan.foto',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                'jabatan.nama_jabatan'
            );

        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $query->where('presensi_dispensasi.nik', $userkaryawan?->nik);
        } elseif (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            }
            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            }
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('presensi_dispensasi.tanggal', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('presensi_dispensasi.status', $request->status);
        }

        $query->orderByRaw("CASE WHEN presensi_dispensasi.status = 'PENDING' THEN 1 ELSE 2 END");
        $query->orderBy('presensi_dispensasi.tanggal', 'desc');

        $dispensasi = $query->paginate(15)->appends($request->all());

        $data['dispensasi'] = $dispensasi;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();

        if ($user->hasRole('karyawan')) {
            return view('dispensasi.index-mobile', $data);
        }

        return view('dispensasi.index', $data);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $qkaryawan = Karyawan::query()->where('status_aktif_karyawan', '1');
        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $qkaryawan->where('nik', $userkaryawan?->nik);
            $data['karyawan'] = $qkaryawan->get();
            return view('dispensasi.create-mobile', $data);
        } elseif (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }

        $data['karyawan'] = $qkaryawan->get();
        return view('dispensasi.create', $data);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $nik = $userkaryawan?->nik;
        } else {
            $nik = $request->nik;
            if (!$user->isSuperAdmin()) {
                $targetKaryawan = Karyawan::where('nik', $nik)->first();
                if (!$targetKaryawan) {
                    return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
                }
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                if (empty($userCabangs) || !in_array($targetKaryawan->kode_cabang, $userCabangs)) {
                    abort(403, 'Anda tidak memiliki akses ke cabang karyawan ini.');
                }
                if (empty($userDepartemens) || !in_array($targetKaryawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke departemen karyawan ini.');
                }
            }
        }

        $request->validate([
            'tanggal' => 'required|date',
            'batas_dispensasi' => 'required',
            'alasan' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Cek duplikasi dispensasi per hari
            $exists = PresensiDispensasi::where('nik', $nik)
                ->where('tanggal', $request->tanggal)
                ->where('status', '!=', 'REJECTED')
                ->exists();

            if ($exists) {
                $msg = 'Pengajuan dispensasi untuk tanggal tersebut sudah ada!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return Redirect::back()->with(messageError($msg));
            }

            PresensiDispensasi::create([
                'nik' => $nik,
                'tanggal' => $request->tanggal,
                'batas_dispensasi' => $request->batas_dispensasi,
                'alasan' => $request->alasan,
                'status' => 'PENDING',
                'approved_by' => null
            ]);

            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Pengajuan dispensasi berhasil dikirim.']);
            }
            if ($user->hasRole('karyawan')) {
                return Redirect::route('dispensasi.index')->with(messageSuccess('Pengajuan dispensasi berhasil dikirim.'));
            }
            return Redirect::back()->with(messageSuccess('Pengajuan dispensasi berhasil dikirim.'));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengajukan dispensasi: ' . $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError('Gagal mengajukan dispensasi: ' . $e->getMessage()));
        }
    }

    private function resolveDispensasiId($id)
    {
        if (is_numeric($id)) {
            return (int) $id;
        }
        try {
            return Crypt::decrypt($id);
        } catch (\Exception $e) {
            return $id;
        }
    }

    public function approve($id)
    {
        $id = $this->resolveDispensasiId($id);
        $dispensasi = PresensiDispensasi::with(['karyawan.jabatan', 'karyawan.departemen', 'karyawan.cabang'])->findOrFail($id);
        $this->authorizeAdminAccess($dispensasi);
        return view('dispensasi.approve', compact('dispensasi'));
    }

    public function storeApprove(Request $request, $id)
    {
        $id = $this->resolveDispensasiId($id);
        $dispensasi = PresensiDispensasi::with('karyawan')->findOrFail($id);
        $this->authorizeAdminAccess($dispensasi);

        $status = $request->status; // 'APPROVED' or 'REJECTED'
        if (empty($status)) {
            if ($request->has('approve') || $request->approve === 'approve') {
                $status = 'APPROVED';
            } elseif ($request->has('tolak') || $request->tolak === 'tolak') {
                $status = 'REJECTED';
            }
        }

        if (!in_array($status, ['APPROVED', 'REJECTED', 'PENDING'])) {
            return Redirect::back()->with(messageError('Status persetujuan tidak valid!'));
        }

        try {
            DB::beginTransaction();

            $dispensasi->update([
                'status' => $status,
                'approved_by' => auth()->id()
            ]);

            // Jika APPROVED, cek apakah sudah ada rekaman presensi pada hari itu
            if ($status === 'APPROVED') {
                $presensi = Presensi::where('nik', $dispensasi->nik)
                    ->where('tanggal', $dispensasi->tanggal)
                    ->first();

                if ($presensi && $presensi->jam_in) {
                    $jamInCarbon = Carbon::parse($presensi->jam_in);
                    $batasCarbon = Carbon::parse($dispensasi->tanggal . ' ' . $dispensasi->batas_dispensasi);

                    // Jika jam_in berada di dalam batas dispensasi, beri label DISPENSASI
                    if ($jamInCarbon->lte($batasCarbon)) {
                        $presensi->update([
                            'is_dispensasi' => 1,
                            'dispensasi_id' => $dispensasi->id,
                            'keterangan' => 'DISPENSASI'
                        ]);
                    }
                }
            }

            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Persetujuan dispensasi berhasil diproses.']);
            }
            return Redirect::back()->with(messageSuccess('Persetujuan dispensasi berhasil diproses.'));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memproses persetujuan: ' . $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError('Gagal memproses persetujuan: ' . $e->getMessage()));
        }
    }

    public function cancelApprove(Request $request, $id)
    {
        $id = $this->resolveDispensasiId($id);
        $dispensasi = PresensiDispensasi::with('karyawan')->findOrFail($id);
        $this->authorizeAdminAccess($dispensasi);

        try {
            DB::beginTransaction();

            // Revert presensi
            Presensi::where('dispensasi_id', $dispensasi->id)
                ->update([
                    'is_dispensasi' => 0,
                    'dispensasi_id' => null,
                    'keterangan' => null
                ]);

            $dispensasi->update([
                'status' => 'PENDING',
                'approved_by' => null
            ]);

            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Persetujuan dispensasi berhasil dibatalkan.']);
            }
            return Redirect::back()->with(messageSuccess('Persetujuan dispensasi berhasil dibatalkan.'));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal membatalkan: ' . $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError('Gagal membatalkan: ' . $e->getMessage()));
        }
    }

    public function destroy(Request $request, $id)
    {
        $id = $this->resolveDispensasiId($id);
        $dispensasi = PresensiDispensasi::with('karyawan')->findOrFail($id);

        if ($dispensasi->status === 'APPROVED') {
            $msg = 'Dispensasi yang sudah disetujui tidak dapat dihapus langsung.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return Redirect::back()->with(messageError($msg));
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userkaryawan || $dispensasi->nik !== $userkaryawan->nik) {
                abort(403, 'Anda tidak memiliki hak untuk menghapus dispensasi ini.');
            }
        } else {
            $this->authorizeAdminAccess($dispensasi);
        }

        $dispensasi->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Pengajuan dispensasi berhasil dihapus.']);
        }
        return Redirect::back()->with(messageSuccess('Pengajuan dispensasi berhasil dihapus.'));
    }

    private function authorizeAdminAccess(PresensiDispensasi $dispensasi): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->hasRole('karyawan')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk persetujuan dispensasi.');
        }

        // Anti Self-Approval Protection: Applicant cannot approve their own request
        $approvalService = app(\App\Services\ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $dispensasi->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui pengajuan dispensasi milik Anda sendiri.');
        }

        if (!$user->isSuperAdmin()) {
            $karyawan = $dispensasi->karyawan ?? Karyawan::where('nik', $dispensasi->nik)->first();
            if (!$karyawan) {
                abort(403, 'Data karyawan tidak ditemukan.');
            }
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (empty($userCabangs) || !in_array($karyawan->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke cabang dispensasi ini.');
            }
            if (empty($userDepartemens) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke departemen dispensasi ini.');
            }
        }
    }
}
