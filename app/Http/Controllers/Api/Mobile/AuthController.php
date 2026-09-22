<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle mobile login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $username = (string)$request->input('username');
        $password = (string)$request->input('password');
        $normalizedUsername = \Illuminate\Support\Str::lower(trim($username));
        $clientIp = (string)$request->ip();

        $accountKey = 'mobile-login-account:' . $normalizedUsername;
        $ipKey = 'mobile-login-ip:' . $clientIp;

        // 1. Account limiter: Max 5 failed attempts per 60 seconds
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($accountKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($accountKey);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan login pada akun ini. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        // 2. IP limiter: Max 20 failed attempts per 60 seconds (prevents password spraying)
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($ipKey, 20)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($ipKey);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan login dari jaringan/perangkat Anda. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        // Check user by username or email
        $user = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($accountKey, 60);
            \Illuminate\Support\Facades\RateLimiter::hit($ipKey, 60);
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // On successful credential check, clear the account throttle
        \Illuminate\Support\Facades\RateLimiter::clear($accountKey);

        // Check if user is linked to Karyawan
        $userKaryawan = $user->userkaryawan;
        if (!$userKaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terdaftar sebagai karyawan'
            ], 403);
        }

        $karyawan = $userKaryawan->karyawan;
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }

        if ($karyawan->status_aktif_karyawan != '1') {
            return response()->json([
                'success' => false,
                'message' => 'Akun karyawan Anda sudah dinonaktifkan / tidak aktif'
            ], 403);
        }

        // Create Sanctum Token
        $token = $user->createToken('mobile-token')->plainTextToken;

        // Fetch associated relationships
        $karyawan->load(['jabatan', 'departemen', 'cabang']);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $karyawan->nama_karyawan ?? $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nik' => $karyawan->nik,
                'jabatan' => $karyawan->jabatan->nama_jabatan ?? null,
                'departemen' => $karyawan->departemen->nama_dept ?? null,
                'cabang' => $karyawan->cabang->nama_cabang ?? null,
                'foto' => $karyawan->foto ? asset('storage/karyawan/' . $karyawan->foto . '?t=' . strtotime($karyawan->updated_at)) : null,
            ]
        ]);
    }

    /**
     * Handle mobile logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Get mobile user profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        $userKaryawan = $user->userkaryawan;
        if (!$userKaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terdaftar sebagai karyawan'
            ], 403);
        }

        $karyawan = $userKaryawan->karyawan;
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }

        $karyawan->load(['jabatan', 'departemen', 'cabang']);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $karyawan->nama_karyawan ?? $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nik' => $karyawan->nik,
                'jabatan' => $karyawan->jabatan->nama_jabatan ?? null,
                'departemen' => $karyawan->departemen->nama_dept ?? null,
                'cabang' => $karyawan->cabang->nama_cabang ?? null,
                'foto' => $karyawan->foto ? asset('storage/karyawan/' . $karyawan->foto . '?t=' . strtotime($karyawan->updated_at)) : null,
            ]
        ]);
    }
}
