<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Cabang;
use App\Models\CompanyPolicy;
use App\Models\Departemen;
use App\Models\EmployeeAsset;
use App\Models\EmployeeDocument;
use App\Models\EmployeeIncident;
use App\Models\EmployeeTraining;
use App\Models\EmployeeWarning;
use App\Models\Karyawan;
use App\Services\TalentGovernanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TalentGovernanceController extends Controller
{
    protected TalentGovernanceService $governanceService;

    public function __construct(TalentGovernanceService $governanceService)
    {
        $this->governanceService = $governanceService;
    }

    // ==========================================
    // 1. PELATIHAN & PENGEMBANGAN (TRAINING)
    // ==========================================
    public function trainingIndex(Request $request)
    {
        $query = EmployeeTraining::with('karyawan.departemen');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhereHas('karyawan', fn($sub) => $sub->where('nama_karyawan', 'like', "%{$s}%"));
            });
        }

        $trainings = $query->orderBy('start_date', 'desc')->paginate(10);
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('kepegawaian.governance.training', compact('trainings', 'karyawans'));
    }

    public function trainingStore(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'title' => 'required|string|max:200',
            'provider' => 'nullable|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration_hours' => 'required|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'certificate_number' => 'nullable|string|max:100',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);

        $this->governanceService->recordTraining($request->all());

        return redirect()->back()->with('success', 'Catatan pelatihan karyawan berhasil disimpan.');
    }

    public function trainingDestroy($id)
    {
        EmployeeTraining::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data pelatihan berhasil dihapus.');
    }

    // ==========================================
    // 2. DISIPLIN & SURAT PERINGATAN (SP)
    // ==========================================
    public function warningIndex(Request $request)
    {
        $query = EmployeeWarning::with(['karyawan.departemen', 'issuer']);

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('sp_number', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhereHas('karyawan', fn($sub) => $sub->where('nama_karyawan', 'like', "%{$s}%"));
            });
        }

        $warnings = $query->orderBy('effective_date', 'desc')->paginate(10);
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('kepegawaian.governance.warning', compact('warnings', 'karyawans'));
    }

    public function warningStore(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'level' => 'required|string|in:TEGURAN_LISAN,SP_1,SP_2,SP_3,SKORSING',
            'effective_date' => 'required|date',
            'violation_description' => 'required|string',
            'pasal_pelanggaran' => 'nullable|string|max:255',
            'action_plan' => 'nullable|string',
        ]);

        $this->governanceService->issueWarning($request->all(), Auth::id());

        return redirect()->back()->with('success', 'Surat Peringatan (SP) berhasil diterbitkan dengan masa berlaku 6 bulan.');
    }

    public function warningDestroy($id)
    {
        EmployeeWarning::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Surat Peringatan berhasil dihapus.');
    }

    // ==========================================
    // 3. BRANKAS DOKUMEN KARYAWAN (DOCUMENTS)
    // ==========================================
    public function documentIndex(Request $request)
    {
        $query = EmployeeDocument::with(['karyawan.departemen', 'uploader']);

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhereHas('karyawan', fn($sub) => $sub->where('nama_karyawan', 'like', "%{$s}%"));
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $types = EmployeeDocument::TYPES;

        return view('kepegawaian.governance.documents', compact('documents', 'karyawans', 'types'));
    }

    public function documentStore(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'document_type' => 'required|string|in:KTP,NPWP,KK,IJAZAH,KONTRAK,SERTIFIKAT,BPJS,OTHER',
            'title' => 'required|string|max:150',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $this->governanceService->uploadDocument(
            $request->nik,
            $request->all(),
            $request->file('document_file'),
            Auth::id()
        );

        return redirect()->back()->with('success', 'Dokumen berkas karyawan berhasil diunggah ke brankas.');
    }

    public function documentDestroy($id)
    {
        EmployeeDocument::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Dokumen berkas karyawan berhasil dihapus.');
    }

    // ==========================================
    // 4. PERATURAN PERUSAHAAN & SOP (POLICIES)
    // ==========================================
    public function policyIndex()
    {
        $policies = CompanyPolicy::orderBy('category')->orderBy('title')->get();
        $categories = CompanyPolicy::CATEGORIES;

        return view('kepegawaian.governance.policies', compact('policies', 'categories'));
    }

    public function policyStore(Request $request)
    {
        $request->validate([
            'policy_code' => 'required|string|max:50|unique:company_policies,policy_code',
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'effective_date' => 'required|date',
            'version' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        CompanyPolicy::create($request->all());

        return redirect()->back()->with('success', 'Kebijakan / SOP Perusahaan berhasil didaftarkan.');
    }

    // ==========================================
    // 5. INVENTARIS & ASET KARYAWAN (ASSETS)
    // ==========================================
    public function assetIndex(Request $request)
    {
        $query = EmployeeAsset::with('karyawan.departemen');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('asset_code', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhereHas('karyawan', fn($sub) => $sub->where('nama_karyawan', 'like', "%{$s}%"));
            });
        }

        $assets = $query->orderBy('assigned_date', 'desc')->paginate(10);
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $categories = EmployeeAsset::CATEGORIES;

        return view('kepegawaian.governance.assets', compact('assets', 'karyawans', 'categories'));
    }

    public function assetStore(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'name' => 'required|string|max:150',
            'category' => 'required|string',
            'serial_number' => 'nullable|string|max:100',
            'assigned_date' => 'required|date',
            'condition' => 'required|string|in:EXCELLENT,GOOD,FAIR,DAMAGED',
            'notes' => 'nullable|string',
        ]);

        $this->governanceService->assignAsset($request->all());

        return redirect()->back()->with('success', 'Aset fasilitas kerja berhasil ditugaskan ke karyawan.');
    }

    public function assetReturn(Request $request, $id)
    {
        $request->validate([
            'condition' => 'required|string|in:EXCELLENT,GOOD,FAIR,DAMAGED',
        ]);

        $this->governanceService->returnAsset((int) $id, $request->condition);

        return redirect()->back()->with('success', 'Aset fasilitas kerja berhasil ditandai telah dikembalikan.');
    }

    // ==========================================
    // 6. PENGUMUMAN INTERNAL (ANNOUNCEMENTS)
    // ==========================================
    public function announcementIndex(Request $request)
    {
        $announcements = Announcement::with('author')->orderBy('is_pinned', 'desc')->orderBy('published_at', 'desc')->paginate(10);
        $departemens = Departemen::orderBy('nama_dept')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $categories = Announcement::CATEGORIES;

        return view('kepegawaian.governance.announcements', compact('announcements', 'departemens', 'cabangs', 'categories'));
    }

    public function announcementStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'category' => 'required|string',
            'is_pinned' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_pinned'] = $request->boolean('is_pinned');

        $this->governanceService->publishAnnouncement($data, Auth::id());

        return redirect()->back()->with('success', 'Pengumuman resmi berhasil dipublikasikan.');
    }

    public function announcementDestroy($id)
    {
        Announcement::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    // ==========================================
    // 7. KASUS & INSIDEN (INCIDENTS)
    // ==========================================
    public function incidentIndex(Request $request)
    {
        $incidents = EmployeeIncident::with(['reporter', 'subject', 'handler'])->orderBy('created_at', 'desc')->paginate(10);
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $categories = EmployeeIncident::CATEGORIES;

        return view('kepegawaian.governance.incidents', compact('incidents', 'karyawans', 'categories'));
    }

    public function incidentStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'incident_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string',
            'reporter_nik' => 'nullable|string|exists:karyawan,nik',
            'subject_nik' => 'nullable|string|exists:karyawan,nik',
        ]);

        $this->governanceService->reportIncident($request->all(), Auth::id());

        return redirect()->back()->with('success', 'Laporan kasus / insiden berhasil didaftarkan.');
    }

    public function incidentResolve(Request $request, $id)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $this->governanceService->resolveIncident((int) $id, $request->resolution_notes, Auth::id());

        return redirect()->back()->with('success', 'Kasus berhasil diselesaikan (Resolved).');
    }
}
