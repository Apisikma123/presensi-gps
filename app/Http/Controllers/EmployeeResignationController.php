<?php

namespace App\Http\Controllers;

use App\Models\EmployeeResignation;
use App\Models\Karyawan;
use App\Services\EmployeeLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class EmployeeResignationController extends Controller
{
    protected EmployeeLifecycleService $lifecycleService;

    public function __construct(EmployeeLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    public function index(Request $request)
    {
        $query = EmployeeResignation::with(['karyawan', 'approver']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($sub) use ($search) {
                      $sub->where('nama_karyawan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kategori_keluar')) {
            $query->where('kategori_keluar', $request->kategori_keluar);
        }

        if ($request->filled('status_clearance')) {
            $query->where('status_clearance', $request->status_clearance);
        }

        $resignations = $query->orderBy('tanggal_keluar', 'desc')->paginate(15);
        $categories = EmployeeResignation::CATEGORIES;

        $stats = [
            'total' => EmployeeResignation::count(),
            'pending_clearance' => EmployeeResignation::where('status_clearance', 'PENDING')->count(),
            'cleared' => EmployeeResignation::where('status_clearance', 'CLEARED')->count(),
        ];

        return view('datamaster.resignation.index', compact('resignations', 'categories', 'stats'));
    }

    public function create(Request $request)
    {
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $categories = EmployeeResignation::CATEGORIES;
        $selectedNik = $request->get('nik');

        return view('datamaster.resignation.create', compact('karyawans', 'categories', 'selectedNik'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'tanggal_pengajuan' => 'required|date',
            'tanggal_keluar' => 'required|date|after_or_equal:tanggal_pengajuan',
            'kategori_keluar' => 'required|string|in:RESIGNED,END_OF_CONTRACT,TERMINATED,RETIRED,DECEASED,OTHER',
            'alasan' => 'nullable|string',
            'status_clearance' => 'required|string|in:PENDING,IN_PROGRESS,CLEARED',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan_hr' => 'nullable|string',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('resignations', 'public');
        }

        $resignation = EmployeeResignation::create([
            'nik' => $request->nik,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'tanggal_keluar' => $request->tanggal_keluar,
            'kategori_keluar' => $request->kategori_keluar,
            'alasan' => $request->alasan,
            'status_clearance' => $request->status_clearance,
            'dokumen' => $dokumenPath,
            'catatan_hr' => $request->catatan_hr,
            'status' => 'APPROVED',
            'approved_by' => Auth::id(),
        ]);

        // Process resignation and deactivate employee
        $this->lifecycleService->processResignation($resignation, true);

        return redirect()->route('resignation.index')->with('success', 'Data pengunduran diri/offboarding berhasil diproses dan status karyawan dinonaktifkan.');
    }

    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $resignation = EmployeeResignation::findOrFail($decryptedId);

        $request->validate([
            'status_clearance' => 'required|string|in:PENDING,IN_PROGRESS,CLEARED',
            'catatan_hr' => 'nullable|string',
        ]);

        $resignation->update([
            'status_clearance' => $request->status_clearance,
            'catatan_hr' => $request->catatan_hr,
        ]);

        return redirect()->route('resignation.index')->with('success', 'Status serah terima / clearance berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $resignation = EmployeeResignation::findOrFail($decryptedId);

        if ($resignation->dokumen && Storage::disk('public')->exists($resignation->dokumen)) {
            Storage::disk('public')->delete($resignation->dokumen);
        }

        $this->lifecycleService->revertResignation($resignation);

        return redirect()->route('resignation.index')->with('success', 'Pengunduran diri dibatalkan dan status karyawan berhasil diaktifkan kembali.');
    }
}
