<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{

    protected $id_type;
    protected function prepareForValidation()
    {
        if (filter_var($this->input('id_user'), FILTER_VALIDATE_EMAIL)) {
            $this->id_type = 'email';
        } else {
            $this->id_type = "username";
        }

        $this->merge(([
            $this->id_type => $this->input('id_user')
        ]));
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_user' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function getIdType(): string
    {
        if (empty($this->id_type)) {
            $this->id_type = filter_var($this->input('id_user'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        }
        return $this->id_type;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $idType = $this->getIdType();
        $credentials = [
            $idType => $this->input('id_user'),
            'password' => $this->input('password'),
        ];

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'id_user' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();
        if ($user && ($user->hasRole('karyawan') || $user->userkaryawan)) {
            $uk = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            if ($uk) {
                $karyawan = \App\Models\Karyawan::where('nik', $uk->nik)->first();
                if (!$karyawan || $karyawan->status_aktif_karyawan != '1' || $karyawan->status_karyawan === 'R' || !empty($karyawan->tanggal_nonaktif)) {
                    Auth::logout();
                    RateLimiter::hit($this->throttleKey());
                    throw ValidationException::withMessages([
                        'id_user' => 'Akun karyawan Anda berstatus tidak aktif atau telah mengundurkan diri (resign). Silakan hubungi administrator.',
                    ]);
                }
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Per-user identifier + IP rate limit (prevents account brute-force without blocking shared Wi-Fi/NAT colleagues)
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'id_user' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $identifier = $this->input('id_user') ?? $this->input('email') ?? $this->input('username') ?? '';
        return Str::transliterate(Str::lower($identifier) . '|' . $this->ip());
    }
}
