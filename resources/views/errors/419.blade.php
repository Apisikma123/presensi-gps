<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir (419) | BrewSync Enterprise</title>
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
            --amber-accent: #b45309;
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
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: var(--amber-accent);
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
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
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
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>

        <div class="status-badge">
            <span>HTTP 419</span>
            <span>•</span>
            <span>SESSION EXPIRED</span>
        </div>

        <h1>Sesi Telah Berakhir</h1>

        <p class="desc">
            Sesi autentikasi Anda telah berakhir demi keamanan data presensi. Silakan masuk kembali ke akun Anda untuk melanjutkan aktivitas.
        </p>

        <a href="{{ route('loginuser') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Masuk Kembali (Login)
        </a>
    </main>
</body>

</html>
