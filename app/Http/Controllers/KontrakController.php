<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Kontrak;
use App\Services\EmployeeLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class KontrakController extends Controller
{
    protected EmployeeLifecycleService $lifecycleService;

    public function __construct(EmployeeLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    public function index(Request $request)
    {
        // Audit statuses
        $this->lifecycleService->recheckContractStatuses();

        $query = Kontrak::with(['karyawan', 'cabang', 'departemen']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kontrak', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($sub) use ($search) {
                      $sub->where('nama_karyawan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_kontrak')) {
            $query->where('jenis_kontrak', $request->jenis_kontrak);
        }

        if ($request->filled('kode_cabang')) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        $stats = [
            'total' => Kontrak::count(),
            'active' => Kontrak::where('status', 'ACTIVE')->count(),
            'expiring' => Kontrak::where('status', 'EXPIRING_SOON')->count(),
            'expired' => Kontrak::where('status', 'EXPIRED')->count(),
        ];

        $kontraks = $query->orderBy('tanggal_mulai', 'desc')->paginate(15);
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get(['nik', 'nama_karyawan', 'kode_cabang', 'kode_dept', 'kode_jabatan']);

        return view('datamaster.kontrak.index', compact('kontraks', 'stats', 'cabangs', 'departemens', 'karyawans'));
    }

    public function create()
    {
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();

        return view('datamaster.kontrak.create', compact('karyawans', 'cabangs', 'departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_kontrak' => 'required|string|max:100|unique:kontrak,no_kontrak',
            'nik' => 'required|string|exists:karyawan,nik',
            'jenis_kontrak' => 'required|string|in:PKWT,PKWTT,PROBATION,INTERNSHIP,FREELANCE',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'reminder_days' => 'nullable|integer|min:1|max:180',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('kontrak', 'public');
        }

        $karyawan = Karyawan::where('nik', $request->nik)->first();

        // Calculate initial status
        $status = 'ACTIVE';
        if ($request->tanggal_selesai) {
            $selesai = Carbon::parse($request->tanggal_selesai);
            $today = Carbon::today();
            $reminderDays = $request->reminder_days ?: 30;

            if ($selesai->lt($today)) {
                $status = 'EXPIRED';
            } elseif ($selesai->diffInDays($today) <= $reminderDays) {
                $status = 'EXPIRING_SOON';
            }
        }

        $kontrak = Kontrak::create([
            'no_kontrak' => trim($request->no_kontrak),
            'nik' => $request->nik,
            'jenis_kontrak' => $request->jenis_kontrak,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?: null,
            'status' => $status,
            'jabatan' => $request->jabatan ?: ($karyawan ? $karyawan->kode_jabatan : null),
            'kode_cabang' => $request->kode_cabang ?: ($karyawan ? $karyawan->kode_cabang : null),
            'kode_dept' => $request->kode_dept ?: ($karyawan ? $karyawan->kode_dept : null),
            'gaji_pokok' => $request->gaji_pokok ?: null,
            'dokumen' => $dokumenPath,
            'keterangan' => $request->keterangan,
            'reminder_days' => $request->reminder_days ?: 30,
            'reminder_sent' => false,
            'created_by' => Auth::id(),
        ]);

        // Sync employee employment type & status
        if ($karyawan) {
            $karyawan->update([
                'employment_type' => $request->jenis_kontrak,
                'status_karyawan' => ($request->jenis_kontrak === 'PKWTT' ? 'T' : 'K'),
            ]);
        }

        return redirect()->route('kontrak.index')->with('success', 'Kontrak kerja berhasil disimpan');
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $kontrak = Kontrak::with('karyawan')->findOrFail($decryptedId);
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();

        return view('datamaster.kontrak.edit', compact('kontrak', 'cabangs', 'departemens'));
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $kontrak = Kontrak::findOrFail($decryptedId);

        $request->validate([
            'no_kontrak' => 'required|string|max:100|unique:kontrak,no_kontrak,' . $kontrak->id,
            'jenis_kontrak' => 'required|string|in:PKWT,PKWTT,PROBATION,INTERNSHIP,FREELANCE',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|string|in:ACTIVE,EXPIRING_SOON,EXPIRED,RENEWED,TERMINATED',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'reminder_days' => 'nullable|integer|min:1|max:180',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
        ]);

        $dokumenPath = $kontrak->dokumen;
        if ($request->hasFile('dokumen')) {
            if ($kontrak->dokumen && Storage::disk('public')->exists($kontrak->dokumen)) {
                Storage::disk('public')->delete($kontrak->dokumen);
            }
            $dokumenPath = $request->file('dokumen')->store('kontrak', 'public');
        }

        $kontrak->update([
            'no_kontrak' => trim($request->no_kontrak),
            'jenis_kontrak' => $request->jenis_kontrak,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?: null,
            'status' => $request->status,
            'jabatan' => $request->jabatan,
            'kode_cabang' => $request->kode_cabang,
            'kode_dept' => $request->kode_dept,
            'gaji_pokok' => $request->gaji_pokok ?: null,
            'dokumen' => $dokumenPath,
            'keterangan' => $request->keterangan,
            'reminder_days' => $request->reminder_days ?: 30,
        ]);

        return redirect()->route('kontrak.index')->with('success', 'Data kontrak kerja berhasil diperbarui');
    }

    public function renew(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $oldKontrak = Kontrak::findOrFail($decryptedId);

        $request->validate([
            'new_no_kontrak' => 'required|string|max:100|unique:kontrak,no_kontrak',
            'new_jenis_kontrak' => 'required|string|in:PKWT,PKWTT,PROBATION,INTERNSHIP,FREELANCE',
            'new_tanggal_mulai' => 'required|date',
            'new_tanggal_selesai' => 'nullable|date|after_or_equal:new_tanggal_mulai',
            'new_gaji_pokok' => 'nullable|numeric|min:0',
            'new_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('new_dokumen')) {
            $dokumenPath = $request->file('new_dokumen')->store('kontrak', 'public');
        }

        // Mark old contract as renewed
        $oldKontrak->update(['status' => 'RENEWED']);

        // Create new contract
        Kontrak::create([
            'no_kontrak' => trim($request->new_no_kontrak),
            'nik' => $oldKontrak->nik,
            'jenis_kontrak' => $request->new_jenis_kontrak,
            'tanggal_mulai' => $request->new_tanggal_mulai,
            'tanggal_selesai' => $request->new_tanggal_selesai ?: null,
            'status' => 'ACTIVE',
            'jabatan' => $oldKontrak->jabatan,
            'kode_cabang' => $oldKontrak->kode_cabang,
            'kode_dept' => $oldKontrak->kode_dept,
            'gaji_pokok' => $request->new_gaji_pokok ?? $oldKontrak->gaji_pokok,
            'dokumen' => $dokumenPath,
            'keterangan' => 'Perpanjangan dari kontrak #' . $oldKontrak->no_kontrak,
            'reminder_days' => $oldKontrak->reminder_days,
            'reminder_sent' => false,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('kontrak.index')->with('success', 'Perpanjangan kontrak berhasil diterbitkan');
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $kontrak = Kontrak::findOrFail($decryptedId);

        if ($kontrak->dokumen && Storage::disk('public')->exists($kontrak->dokumen)) {
            Storage::disk('public')->delete($kontrak->dokumen);
        }

        $kontrak->delete();

        return redirect()->route('kontrak.index')->with('success', 'Kontrak kerja berhasil dihapus');
    }
}
