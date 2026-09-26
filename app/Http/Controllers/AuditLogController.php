<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    protected AuditLogService $auditService;

    public function __construct(AuditLogService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display audit trail log index
     */
    public function index(Request $request)
    {
        $filters = $request->only(['module', 'action', 'search', 'date_from', 'date_to', 'user_id']);
        $logs = $this->auditService->getFiltered($filters, 25);
        $metrics = $this->auditService->getSummaryMetrics();

        $modules = AuditLog::select('module')->distinct()->pluck('module')->sort()->values();
        $actions = AuditLog::select('action')->distinct()->pluck('action')->sort()->values();

        return view('settings.audit_logs.index', compact('logs', 'metrics', 'filters', 'modules', 'actions'));
    }

    /**
     * Export audit logs to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only(['module', 'action', 'search', 'date_from', 'date_to', 'user_id']);
        $logs = $this->auditService->getFiltered($filters, 2000);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="presence_audit_logs_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Waktu', 'User', 'Aksi', 'Modul', 'ID Rekord', 'IP Address', 'Detail Perubahan']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                    $log->user ? $log->user->name : 'Sistem / Anonim',
                    $log->action,
                    $log->module,
                    $log->record_id ?? '-',
                    $log->ip_address ?? '-',
                    is_array($log->details) ? json_encode($log->details, JSON_UNESCAPED_UNICODE) : ($log->details ?? '-'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
