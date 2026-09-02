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
        if (Auth::check() && Auth::user()->hasRole('karyawan')) {
            $userId = Auth::id();
            $isValid = \Illuminate\Support\Facades\Cache::remember('user_karyawan_valid_' . $userId, 300, function () use ($userId) {
                $userkaryawan = Userkaryawan::where('id_user', $userId)->first();
                if (!$userkaryawan) {
                    return false;
                }
                return Karyawan::where('nik', $userkaryawan->nik)->exists();
            });

            if (!$isValid) {
                \Illuminate\Support\Facades\Cache::forget('user_karyawan_valid_' . $userId);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('loginuser')
                    ->with('error', 'Akun karyawan Anda tidak valid. Silahkan hubungi administrator.');
            }
        }

        return $next($request);
    }
}
