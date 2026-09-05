<?php

namespace App\Http\Middleware;

use App\Models\Karyawan;
use App\Models\Userkaryawan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckKaryawanExists
{
    /**
     * Jika user dengan role karyawan login tetapi data karyawan-nya
     * sudah tidak ada di tabel karyawan, langsung logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && ($user->hasRole('karyawan') || $user->userkaryawan)) {
            $userId = $user->id;
            $isValid = \Illuminate\Support\Facades\Cache::remember('user_karyawan_valid_' . $userId, 300, function () use ($userId) {
                $userkaryawan = Userkaryawan::where('id_user', $userId)->first();
                if (!$userkaryawan) {
                    return false;
                }
                return Karyawan::where('nik', $userkaryawan->nik)
                    ->where('status_aktif_karyawan', '1')
                    ->exists();
            });

            if (!$isValid) {
                \Illuminate\Support\Facades\Cache::forget('user_karyawan_valid_' . $userId);

                // Revoke Sanctum tokens if user has any
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun karyawan Anda sudah dinonaktifkan / tidak aktif.'
                    ], 403);
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('loginuser')
                    ->with('error', 'Akun karyawan Anda sudah dinonaktifkan. Silahkan hubungi administrator.');
            }
        }

        return $next($request);
    }
}
