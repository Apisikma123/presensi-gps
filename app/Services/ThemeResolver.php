<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Pengaturanumum;
use Illuminate\Support\Facades\Cache;

class ThemeResolver
{
    const DEFAULT_PRIMARY = '#3C2A21';
    const DEFAULT_SECONDARY = '#634832';
    const DEFAULT_ACCENT = '#4A6741';

    private static ?array $memoryCache = null;

    /**
     * Current resolved theme
     */
    public static function current(): array
    {
        return self::resolve();
    }

    /**
     * Resolve active theme colors with auto-contrast and semantic statuses
     *
     * @param string|null $overridePrimary Optional direct primary hex for testing/preview
     * @param string|null $overrideSecondary Optional direct secondary hex for testing/preview
     * @return array
     */
    public static function resolve(?string $overridePrimary = null, ?string $overrideSecondary = null): array
    {
        $buildTheme = function (?string $p = null, ?string $s = null) {
            $company = CompanySetting::getSetting();
            $general = Pengaturanumum::getSetting();

            $primary = self::sanitizeHex($p ?: ($company?->theme_color_primary ?: ($general?->theme_color_1 ?: self::DEFAULT_PRIMARY)), self::DEFAULT_PRIMARY);
            $secondary = self::sanitizeHex($s ?: ($company?->theme_color_secondary ?: ($general?->theme_color_2 ?: self::DEFAULT_SECONDARY)), self::DEFAULT_SECONDARY);
            $accent = self::DEFAULT_ACCENT; // Matcha Green

            $primaryRgb = self::hexToRgb($primary);
            $secondaryRgb = self::hexToRgb($secondary);
            $accentRgb = self::hexToRgb($accent);

            $primaryContrast = self::calculateContrastColor($primary);
            $secondaryContrast = self::calculateContrastColor($secondary);

            $isLight = ($primaryContrast === '#1A1C1C');
            $sidebarBg = $isLight ? '#1E293B' : self::adjustBrightness($primary, -35);
            $sidebarSubBg = $isLight ? '#0F172A' : self::adjustBrightness($primary, -50);
            $sidebarActiveBg = $primary;
            $sidebarActiveColor = $primaryContrast;
            $sidebarText = '#D3C3BD';
            $sidebarHeader = '#AA9084';
            $sidebarBorder = "rgba({$primaryRgb}, 0.20)";

            return [
                'primary' => $primary,
                'primary_rgb' => $primaryRgb,
                'primary_contrast' => $primaryContrast,
                'primary_hover' => self::adjustBrightness($primary, -15),
                'primary_soft' => "rgba({$primaryRgb}, 0.08)",
                'primary_border' => "rgba({$primaryRgb}, 0.18)",

                'secondary' => $secondary,
                'secondary_rgb' => $secondaryRgb,
                'secondary_contrast' => $secondaryContrast,
                'secondary_hover' => self::adjustBrightness($secondary, -15),
                'secondary_soft' => "rgba({$secondaryRgb}, 0.08)",

                'accent' => $accent,
                'accent_rgb' => $accentRgb,

                // Semantic statuses (STRICTLY INDEPENDENT from theme)
                'success' => '#4A6741',
                'status_success' => '#4A6741',
                'success_soft' => 'rgba(74, 103, 65, 0.12)',
                'danger' => '#BA1A1A',
                'status_danger' => '#BA1A1A',
                'danger_soft' => 'rgba(186, 26, 26, 0.12)',
                'warning' => '#B45309',
                'status_warning' => '#B45309',
                'warning_soft' => 'rgba(180, 83, 9, 0.12)',
                'info' => '#2563EB',
                'status_info' => '#2563EB',
                'info_soft' => 'rgba(37, 99, 235, 0.12)',

                // Neutral tokens
                'canvas' => '#FAF9F8',
                'surface' => '#FFFFFF',
                'text_primary' => '#1A1C1C',
                'text_secondary' => '#755841',
                'border' => "rgba({$primaryRgb}, 0.08)",
                'border_hover' => "rgba({$primaryRgb}, 0.16)",

                // Dynamic Sidebar Tokens (Zero Hardcode)
                'sidebar_bg' => $sidebarBg,
                'sidebar_sub_bg' => $sidebarSubBg,
                'sidebar_active_bg' => $sidebarActiveBg,
                'sidebar_active_color' => $sidebarActiveColor,
                'sidebar_text' => $sidebarText,
                'sidebar_header' => $sidebarHeader,
                'sidebar_border' => $sidebarBorder,
            ];
        };

        if ($overridePrimary !== null || $overrideSecondary !== null) {
            return $buildTheme($overridePrimary, $overrideSecondary);
        }

        if (static::$memoryCache !== null) {
            return static::$memoryCache;
        }

        return static::$memoryCache = Cache::remember('global_app_theme', 3600, function () use ($buildTheme) {
            return $buildTheme();
        });
    }

    /**
     * Invalidate cached theme
     */
    public static function forgetCache(): void
    {
        static::$memoryCache = null;
        Cache::forget('global_app_theme');
    }

    /**
     * Calculate accessible foreground text color (auto-contrast)
     * Returns #1A1C1C for light backgrounds, #FFFFFF for dark backgrounds.
     */
    public static function calculateContrastColor(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        // ITU-R BT.709 relative luminance formula
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return ($luminance > 0.55) ? '#1A1C1C' : '#FFFFFF';
    }

    /**
     * Convert Hex string to "R, G, B" string
     */
    public static function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "{$r}, {$g}, {$b}";
    }

    /**
     * Safely validate and sanitize Hex color
     */
    public static function sanitizeHex(?string $hex, string $fallback = self::DEFAULT_PRIMARY): string
    {
        if (empty($hex)) {
            return $fallback;
        }

        $clean = trim($hex);
        if (!str_starts_with($clean, '#')) {
            $clean = '#' . $clean;
        }

        if (preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $clean)) {
            return strtoupper($clean);
        }

        return $fallback;
    }

    /**
     * Adjust color brightness for hover states
     */
    public static function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, (int) round($r + ($r * $percent / 100))));
        $g = max(0, min(255, (int) round($g + ($g * $percent / 100))));
        $b = max(0, min(255, (int) round($b + ($b * $percent / 100))));

        return sprintf("#%02X%02X%02X", $r, $g, $b);
    }
}
