<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Gracefully handle expired CSRF token (419 Page Expired / Sesi Berakhir)
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir, silakan muat ulang halaman.',
                    'csrf_token' => csrf_token()
                ], 419);
            }
            return redirect()->route('loginuser')->with('error', 'Sesi login telah diperbarui. Silakan login kembali.');
        });
    }
}
