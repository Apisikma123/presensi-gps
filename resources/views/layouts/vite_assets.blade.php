@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
    $builtCss = $manifest['resources/css/app.css']['file'] ?? null;
    $builtJs = $manifest['resources/js/app.js']['file'] ?? null;
@endphp

@if ($builtCss && file_exists(public_path('build/' . $builtCss)))
    <link rel="stylesheet" href="{{ asset('build/' . $builtCss) }}?v={{ filemtime(public_path('build/' . $builtCss)) }}">
@else
    @vite(['resources/css/app.css'])
@endif

@if ($builtJs && file_exists(public_path('build/' . $builtJs)))
    <script type="module" src="{{ asset('build/' . $builtJs) }}?v={{ filemtime(public_path('build/' . $builtJs)) }}"></script>
@else
    @vite(['resources/js/app.js'])
@endif
