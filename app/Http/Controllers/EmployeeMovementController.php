<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Division;
use App\Models\EmployeeMovement;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Services\EmployeeLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class EmployeeMovementController extends Controller
{
    protected EmployeeLifecycleService $lifecycleService;

    public function __construct(EmployeeLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    public function index(Request $request)
    {
        $query = EmployeeMovement::with(['karyawan', 'approver', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_sk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($sub) use ($search) {
                      $sub->where('nama_karyawan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $movements = $query->orderBy('effective_date', 'desc')->paginate(15);
        $types = EmployeeMovement::TYPES;

        return view('datamaster.movement.index', compact('movements', 'types'));
    }

    public function create(Request $request)
    {
        $selectedNik = $request->get('nik');
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();
        $divisions = Division::where('is_active', true)->orderBy('nama_divisi')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $types = EmployeeMovement::TYPES;

        $targetKaryawan = null;
        if ($selectedNik) {
            $targetKaryawan = Karyawan::with(['cabang', 'departemen', 'division', 'jabatan', 'supervisor'])->where('nik', $selectedNik)->first();
        }

        return view('datamaster.movement.create', compact(
            'karyawans',
            'cabangs',
            'departemens',
            'divisions',
            'jabatans',
            'types',
            'selectedNik',
            'targetKaryawan'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'movement_type' => 'required|string|in:BRANCH_TRANSFER,DEPT_TRANSFER,DIVISION_CHANGE,POSITION_CHANGE,PROMOTION,DEMOTION,SUPERVISOR_CHANGE,STATUS_CHANGE,SALARY_CHANGE',
            'effective_date' => 'required|date',
            'no_sk' => 'nullable|string|max:100',
            'reason' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $karyawan = Karyawan::where('nik', $request->nik)->firstOrFail();

        // Snapshot old values
        $oldValues = [
            'kode_cabang' => $karyawan->kode_cabang,
            'kode_dept' => $karyawan->kode_dept,
            'kode_divisi' => $karyawan->kode_divisi,
            'kode_jabatan' => $karyawan->kode_jabatan,
            'grade_level' => $karyawan->grade_level,
            'direct_supervisor_nik' => $karyawan->direct_supervisor_nik,
            'employment_type' => $karyawan->employment_type,
            'status_karyawan' => $karyawan->status_karyawan,
        ];

        // Gather new values based on what was submitted
        $newValues = [];
        if ($request->filled('new_kode_cabang')) {
            $newValues['kode_cabang'] = $request->new_kode_cabang;
        }
        if ($request->filled('new_kode_dept')) {
            $newValues['kode_dept'] = $request->new_kode_dept;
        }
        if ($request->filled('new_kode_divisi')) {
            $newValues['kode_divisi'] = $request->new_kode_divisi;
        }
        if ($request->filled('new_kode_jabatan')) {
            $newValues['kode_jabatan'] = $request->new_kode_jabatan;
        }
        if ($request->filled('new_grade_level')) {
            $newValues['grade_level'] = $request->new_grade_level;
        }
        if ($request->filled('new_supervisor_nik')) {
            $newValues['direct_supervisor_nik'] = $request->new_supervisor_nik;
        }
        if ($request->filled('new_employment_type')) {
            $newValues['employment_type'] = $request->new_employment_type;
        }
        if ($request->filled('new_status_karyawan')) {
            $newValues['status_karyawan'] = $request->new_status_karyawan;
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('movements', 'public');
        }

        $movement = EmployeeMovement::create([
            'no_sk' => $request->no_sk ? trim($request->no_sk) : null,
            'nik' => $request->nik,
            'movement_type' => $request->movement_type,
            'effective_date' => $request->effective_date,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $request->reason,
            'document_path' => $documentPath,
            'status' => 'PENDING',
            'approved_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);

        // Apply immediately if effective date has arrived
        $this->lifecycleService->applyMovement($movement, true);

        return redirect()->route('movement.index')->with('success', 'Riwayat mutasi/promosi berhasil dicatat.');
    }

    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $movement = EmployeeMovement::with(['karyawan', 'approver', 'creator'])->findOrFail($decryptedId);

        return view('datamaster.movement.show', compact('movement'));
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $movement = EmployeeMovement::findOrFail($decryptedId);

        if ($movement->document_path && Storage::disk('public')->exists($movement->document_path)) {
            Storage::disk('public')->delete($movement->document_path);
        }

        $movement->delete();

        return redirect()->route('movement.index')->with('success', 'Catatan mutasi berhasil dihapus.');
    }
}
