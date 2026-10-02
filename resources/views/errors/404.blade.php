@php
    $theme = $theme ?? \App\Services\ThemeResolver::resolve();
    $setting = \App\Models\Pengaturanumum::first();
    $appName = $company_setting->company_name ?? ($setting->nama_app ?? config('app.name', 'Presensi GPS'));
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan (404) | {{ $appName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --surface-bg: #f8fafc;
            --card-bg: #ffffff;
            --primary: {{ $theme['primary'] ?? '#1B365D' }};
            --primary-hover: {{ $theme['primary_hover'] ?? '#142946' }};
            --primary-contrast: {{ $theme['primary_contrast'] ?? '#ffffff' }};
            --secondary: {{ $theme['secondary'] ?? '#4B6B94' }};
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-subtle: rgba({{ $theme['primary_rgb'] ?? '15, 23, 42' }}, 0.10);
            --sky-accent: #0284c7;
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.03);
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
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: var(--sky-accent);
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
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
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
            min-height: 48px;
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            font-family: inherit;
        }

        .btn:active {
            transform: scale(0.985);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--primary-contrast);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            color: var(--primary-contrast);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--text-main);
            border: 1.5px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
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
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>

        <div class="status-badge">
            <span>HTTP 404</span>
            <span>•</span>
            <span>NOT FOUND</span>
        </div>

        <h1>Halaman Tidak Ditemukan</h1>

        <p class="desc">
            Halaman yang Anda tuju mungkin telah dipindahkan, dihapus, atau tautan yang Anda masukkan salah.
        </p>

        <div class="actions-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>

            <button type="button" onclick="history.back()" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Halaman Sebelumnya
            </button>
        </div>
    </main>
</body>

</html>
