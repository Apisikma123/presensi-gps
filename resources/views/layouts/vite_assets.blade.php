@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
    $builtCss = $manifest['resources/css/app.css']['file'] ?? null;
    $builtJs = $manifest['resources/js/app.js']['file'] ?? null;
@endphp

@if ($builtCss)
    <link rel="stylesheet" href="{{ asset('build/' . $builtCss) }}">
@else
    @vite(['resources/css/app.css'])
@endif

@if ($builtJs)
    <script type="module" src="{{ asset('build/' . $builtJs) }}"></script>
@else
    @vite(['resources/js/app.js'])
@endif
