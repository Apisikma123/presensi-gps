<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HostingReadinessService;

class HostingReadinessController extends Controller
{
    /**
     * Display protected hosting readiness diagnostic report.
     * Controlled by feature flag HOSTING_READINESS_ENABLED (default false -> 404).
     */
    public function index(Request $request, HostingReadinessService $service)
    {
        $enabled = filter_var(config('app.hosting_readiness_enabled', false), FILTER_VALIDATE_BOOLEAN)
            || config('app.debug', false);

        if (!$enabled) {
            abort(404);
        }

        $data = $service->runAudit();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json($data);
        }

        return view('settings.hosting_readiness', compact('data'));
    }
}
