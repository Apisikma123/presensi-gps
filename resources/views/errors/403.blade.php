@php
    $setting = \App\Models\Pengaturanumum::first();
    $expiredDateStr = '';
    if ($setting && $setting->expired) {
        $expiredDateStr = \Carbon\Carbon::parse($setting->expired)->translatedFormat('d F Y');
    }
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak (403) | BrewSync Enterprise</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --surface-bg: #faf9f8;
            --card-bg: #ffffff;
            --primary: #25160e;
            --primary-hover: #3c2a21;
            --text-main: #1a1c1c;
            --text-muted: #64748b;
            --border-subtle: rgba(60, 42, 33, 0.08);
            --error: #ba1a1a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--surface-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
        }

        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(60, 42, 33, 0.04), 0 1px 3px rgba(60, 42, 33, 0.03);
            width: 100%;
            max-width: 460px;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            border-radius: 8px;
            background: #fef2f2;
            border: 1px solid #fecdd3;
            color: #b91c1c;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .icon-symbol {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--primary);
            margin-bottom: 0.75rem;
            letter-spacing: -0.01em;
        }

        p.desc {
            font-size: 0.95rem;
            line-height: 1.55;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
        }

        .expired-notice {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.35rem 0.75rem;
            background: #fff1f2;
            border-radius: 6px;
            color: var(--error);
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }

        .actions-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
            font-family: inherit;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--text-main);
            border: 1px solid var(--border-subtle);
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: rgba(60, 42, 33, 0.18);
        }

        .btn-contact {
            background: #059669;
            color: #ffffff;
        }

        .btn-contact:hover {
            background: #047857;
        }

        .btn-danger {
            background: #dc2626;
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <main class="error-card">
        <div class="icon-symbol">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>

        <div class="status-badge">
            <span>HTTP 403</span>
            <span>•</span>
            <span>FORBIDDEN</span>
        </div>

        <h1>Akses Terbatas</h1>

        <p class="desc">
            {{ $exception->getMessage() ?: 'Maaf, akun Anda tidak memiliki hak otorisasi untuk mengakses halaman ini.' }}
            @if(!empty($expiredDateStr) && (str_contains(strtolower($exception->getMessage()), 'kadaluarsa') || str_contains(strtolower($exception->getMessage()), 'expired')))
                <br>
                <span class="expired-notice">Tanggal Kadaluarsa: {{ $expiredDateStr }}</span>
            @endif
        </p>

        <div class="actions-group">
            @if(str_contains(strtolower($exception->getMessage()), 'kadaluarsa') || str_contains(strtolower($exception->getMessage()), 'expired'))
                <a href="https://wa.me/6289670444321" target="_blank" class="btn btn-contact">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.446L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.965C16.57 1.978 14.1 1.957 12.006 1.957c-5.437 0-9.863 4.373-9.867 9.803-.001 1.812.489 3.585 1.42 5.161l-.944 3.449 3.524-.925zm11.233-6.52c-.29-.145-1.72-.848-1.986-.944-.266-.096-.46-.145-.653.145-.193.29-.747.944-.915 1.137-.168.193-.336.217-.626.072-1.353-.678-2.222-1.229-3.003-2.57-.193-.331.193-.307.553-1.017.06-.12.03-.223-.015-.32-.045-.096-.46-1.109-.63-1.522-.165-.397-.333-.343-.46-.349-.118-.005-.253-.006-.388-.006-.135 0-.356.05-.542.253-.186.203-.71.694-.71 1.694 0 1.001.728 1.968.829 2.103.102.135 1.433 2.188 3.473 3.067.485.209.864.335 1.161.429.489.156.935.134 1.287.082.393-.058 1.72-.703 1.962-1.382.242-.678.242-1.261.17-1.382-.072-.12-.266-.217-.556-.363z"/>
                    </svg>
                    Hubungi Administrator
                </a>
            @endif

            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar (Logout)
                    </button>
                </form>
            @endauth
        </div>
    </main>
</body>

</html>
