<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\CompanyPolicy;
use App\Models\EmployeeAsset;
use App\Models\EmployeeDocument;
use App\Models\EmployeeIncident;
use App\Models\EmployeeTraining;
use App\Models\EmployeeWarning;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TalentGovernanceService
{
    /**
     * Record employee training
     */
    public function recordTraining(array $data): EmployeeTraining
    {
        $code = 'TRN-' . date('Ym') . '-' . str_pad((string) (EmployeeTraining::count() + 1), 4, '0', STR_PAD_LEFT);

        return EmployeeTraining::create([
            'training_code' => $data['training_code'] ?? $code,
            'nik' => $data['nik'],
            'title' => $data['title'],
            'provider' => $data['provider'] ?? 'Internal HR',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'duration_hours' => $data['duration_hours'] ?? 8,
            'cost' => $data['cost'] ?? 0,
            'certificate_number' => $data['certificate_number'] ?? null,
            'certificate_file' => $data['certificate_file'] ?? null,
            'status' => $data['status'] ?? 'COMPLETED',
            'score' => $data['score'] ?? null,
        ]);
    }

    /**
     * Issue disciplinary warning letter (SP 1, SP 2, SP 3)
     * Auto sets standard 6-month validity period per Indonesian Labor Law
     */
    public function issueWarning(array $data, ?int $issuedBy = null): EmployeeWarning
    {
        $effective = isset($data['effective_date']) ? Carbon::parse($data['effective_date']) : Carbon::today();
        $expiry = isset($data['expiry_date']) ? Carbon::parse($data['expiry_date']) : $effective->copy()->addMonths(6);

        $spNumber = $data['sp_number'] ?? ('SP/' . date('Y/m/') . str_pad((string) (EmployeeWarning::count() + 1), 4, '0', STR_PAD_LEFT));

        return EmployeeWarning::create([
            'sp_number' => $spNumber,
            'nik' => $data['nik'],
            'level' => $data['level'] ?? 'SP_1',
            'incident_date' => $data['incident_date'] ?? Carbon::today(),
            'effective_date' => $effective,
            'expiry_date' => $expiry,
            'violation_description' => $data['violation_description'],
            'pasal_pelanggaran' => $data['pasal_pelanggaran'] ?? null,
            'action_plan' => $data['action_plan'] ?? null,
            'document_file' => $data['document_file'] ?? null,
            'status' => 'ACTIVE',
            'issued_by' => $issuedBy,
        ]);
    }

    /**
     * Store employee vault document
     */
    public function uploadDocument(string $nik, array $data, $file = null, ?int $uploadedBy = null): EmployeeDocument
    {
        $filePath = null;
        $fileSize = null;

        if ($file) {
            $filePath = $file->store("documents/{$nik}", 'public');
            $fileSize = (int) round($file->getSize() / 1024);
        } elseif (isset($data['file_path'])) {
            $filePath = $data['file_path'];
        }

        return EmployeeDocument::create([
            'nik' => $nik,
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'file_path' => $filePath ?? '',
            'file_size_kb' => $fileSize,
            'expiry_date' => $data['expiry_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    /**
     * Assign equipment/asset to employee
     */
    public function assignAsset(array $data): EmployeeAsset
    {
        $code = $data['asset_code'] ?? ('AST-' . str_pad((string) (EmployeeAsset::count() + 1), 4, '0', STR_PAD_LEFT));

        return EmployeeAsset::create([
            'asset_code' => $code,
            'nik' => $data['nik'],
            'name' => $data['name'],
            'category' => $data['category'] ?? 'HARDWARE',
            'serial_number' => $data['serial_number'] ?? null,
            'assigned_date' => $data['assigned_date'] ?? Carbon::today(),
            'condition' => $data['condition'] ?? 'GOOD',
            'notes' => $data['notes'] ?? null,
            'status' => 'ASSIGNED',
        ]);
    }

    /**
     * Mark asset returned
     */
    public function returnAsset(int $assetId, string $condition = 'GOOD'): EmployeeAsset
    {
        $asset = EmployeeAsset::findOrFail($assetId);
        $asset->status = 'RETURNED';
        $asset->returned_date = Carbon::today();
        $asset->condition = $condition;
        $asset->save();
        return $asset;
    }

    /**
     * Publish company announcement
     */
    public function publishAnnouncement(array $data, ?int $createdBy = null): Announcement
    {
        return Announcement::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'] ?? 'GENERAL',
            'kode_dept' => $data['kode_dept'] ?? null,
            'kode_cabang' => $data['kode_cabang'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'published_at' => $data['published_at'] ?? Carbon::now(),
            'expires_at' => $data['expires_at'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? false,
            'created_by' => $createdBy,
        ]);
    }

    /**
     * File HR incident / case
     */
    public function reportIncident(array $data, ?int $handledBy = null): EmployeeIncident
    {
        $caseNo = 'CASE-' . date('Ym') . '-' . str_pad((string) (EmployeeIncident::count() + 1), 4, '0', STR_PAD_LEFT);

        return EmployeeIncident::create([
            'case_number' => $data['case_number'] ?? $caseNo,
            'reporter_nik' => $data['reporter_nik'] ?? null,
            'subject_nik' => $data['subject_nik'] ?? null,
            'title' => $data['title'],
            'incident_date' => $data['incident_date'] ?? Carbon::today(),
            'category' => $data['category'] ?? 'DISCIPLINARY',
            'description' => $data['description'],
            'status' => 'OPEN',
            'handled_by' => $handledBy,
        ]);
    }

    /**
     * Resolve HR incident
     */
    public function resolveIncident(int $incidentId, string $resolutionNotes, ?int $handledBy = null): EmployeeIncident
    {
        $incident = EmployeeIncident::findOrFail($incidentId);
        $incident->status = 'RESOLVED';
        $incident->resolution_notes = $resolutionNotes;
        $incident->resolved_at = Carbon::now();
        if ($handledBy) {
            $incident->handled_by = $handledBy;
        }
        $incident->save();
        return $incident;
    }

    /**
     * Get talent governance overview stats
     */
    public function getGovernanceStats(): array
    {
        return [
            'total_trainings' => EmployeeTraining::count(),
            'active_warnings' => EmployeeWarning::where('status', 'ACTIVE')->count(),
            'total_documents' => EmployeeDocument::count(),
            'assigned_assets' => EmployeeAsset::where('status', 'ASSIGNED')->count(),
            'open_incidents' => EmployeeIncident::where('status', 'OPEN')->count(),
        ];
    }
}
