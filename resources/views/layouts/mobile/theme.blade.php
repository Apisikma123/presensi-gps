@php
    $theme = \App\Services\ThemeResolver::resolve();
    $t = [
        'primary' => $theme['primary'],
        'primary_light' => $theme['secondary'],
        'bg_body' => $theme['canvas'],
        'surface' => $theme['surface'],
        'text_primary' => $theme['text_primary'],
        'text_secondary' => $theme['text_secondary'],
        'border' => $theme['border'],
        'amber' => $theme['warning'],
        'crimson' => $theme['danger'],
        'matcha' => $theme['accent'],
        'primary_contrast' => $theme['primary_contrast'],
        'primary_soft' => $theme['primary_soft'],
    ];
    $isDark = false;
    $scheme = 'enterprise';

    // Share variables globally
    view()->share('isDark', $isDark);
    view()->share('t', $t);
    view()->share('theme', $theme);
    view()->share('scheme', $scheme);
@endphp
