<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /**
     * Handle an incoming request and ensure the specified module feature is enabled.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $moduleCode
     */
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        // Support checking multiple modules (OR logic if pipe, e.g. "payroll|reimbursement")
        $modules = explode('|', $moduleCode);
        $anyEnabled = false;

        foreach ($modules as $mod) {
            $trimmed = trim($mod);
            if (is_module_enabled($trimmed, false)) {
                $anyEnabled = true;
                break;
            }
        }

        if (!$anyEnabled) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'code' => 403,
                    'message' => "Modul '{$moduleCode}' sedang dinonaktifkan oleh administrator.",
                ], 403);
            }

            if (!auth()->check()) {
                return redirect()->guest(route('login'))->with('warning', 'Silakan login terlebih dahulu.');
            }

            abort(403, "Modul '{$moduleCode}' sedang dinonaktifkan oleh Administrator.");
        }

        return $next($request);
    }
}
