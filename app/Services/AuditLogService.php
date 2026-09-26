<?php

namespace App\Services;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Record an audit event
     */
    public function log(
        string $action,
        string $module,
        ?string $recordId = null,
        array|string|null $details = null,
        ?int $userId = null
    ): AuditLog {
        $ip = Request::ip() ?? '127.0.0.1';
        $agent = Request::userAgent() ?? 'CLI/System';
        $resolvedUserId = $userId ?? Auth::id();
        if ($resolvedUserId && !\App\Models\User::where('id', $resolvedUserId)->exists()) {
            $resolvedUserId = null;
        }

        $detailsPayload = is_array($details)
            ? $details
            : ($details ? ['message' => $details] : null);

        return AuditLog::create([
            'user_id' => $resolvedUserId,
            'action' => strtoupper(trim($action)),
            'module' => strtolower(trim($module)),
            'record_id' => $recordId ? (string) $recordId : null,
            'ip_address' => $ip,
            'user_agent' => substr($agent, 0, 500),
            'details' => $detailsPayload,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * Query audit logs with flexible filtering
     */
    public function getFiltered(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = AuditLog::with('user')->orderBy('id', 'desc');

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', strtoupper($filters['action']));
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $query->where(function ($q) use ($s) {
                $q->where('record_id', 'like', "%{$s}%")
                  ->orWhere('details', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        return $query->paginate($perPage);
    }

    /**
     * Get aggregate metrics for the security dashboard
     */
    public function getSummaryMetrics(): array
    {
        $today = Carbon::today();

        $totalLogs = AuditLog::count();
        $logsToday = AuditLog::where('created_at', '>=', $today)->count();
        $uniqueUsersToday = AuditLog::where('created_at', '>=', $today)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $topModules = AuditLog::selectRaw('module, COUNT(*) as count')
            ->groupBy('module')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'module')
            ->toArray();

        $topActions = AuditLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'action')
            ->toArray();

        return [
            'total_logs' => $totalLogs,
            'logs_today' => $logsToday,
            'unique_users_today' => $uniqueUsersToday,
            'top_modules' => $topModules,
            'top_actions' => $topActions,
        ];
    }
}
