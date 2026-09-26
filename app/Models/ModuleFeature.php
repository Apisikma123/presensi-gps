<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ModuleFeature extends Model
{
    use HasFactory;

    protected $table = 'module_features';
    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_entitled' => 'boolean',
        'is_locked' => 'boolean',
        'config' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public static array $canonicalMap = [
        'face' => 'face_recognition',
        'face_recognition' => 'face_recognition',
        'loan' => 'loans',
        'loans' => 'loans',
        'document' => 'documents',
        'documents' => 'documents',
        'warning' => 'discipline',
        'warnings' => 'discipline',
        'discipline' => 'discipline',
        'asset' => 'asset_assignment',
        'assets' => 'asset_assignment',
        'asset_assignment' => 'asset_assignment',
        'announcement' => 'announcements',
        'announcements' => 'announcements',
        'contract' => 'contracts',
        'contracts' => 'contracts',
        'movement' => 'movements',
        'movements' => 'movements',
        'resignation' => 'offboarding',
        'resignations' => 'offboarding',
        'offboarding' => 'offboarding',
        'policy' => 'governance',
        'incident' => 'governance',
        'incidents' => 'governance',
        'governance' => 'governance',
    ];

    public static array $aliasMap = [
        'face' => ['face_recognition'],
        'face_recognition' => ['face'],
        'loan' => ['loans'],
        'loans' => ['loan'],
        'document' => ['documents'],
        'documents' => ['document'],
        'warning' => ['warnings', 'discipline'],
        'warnings' => ['warning', 'discipline'],
        'discipline' => ['warning', 'warnings'],
        'asset' => ['assets', 'asset_assignment'],
        'assets' => ['asset', 'asset_assignment'],
        'asset_assignment' => ['asset', 'assets'],
        'announcement' => ['announcements'],
        'announcements' => ['announcement'],
        'contract' => ['contracts'],
        'contracts' => ['contract'],
        'movement' => ['movements'],
        'movements' => ['movement'],
        'resignation' => ['resignations', 'offboarding'],
        'resignations' => ['resignation', 'offboarding'],
        'offboarding' => ['resignation', 'resignations'],
        'governance' => ['policy', 'incident', 'incidents'],
        'policy' => ['governance'],
        'incident' => ['governance', 'incidents'],
        'incidents' => ['governance', 'incident'],
    ];

    /**
     * Resolve any alias to its single canonical module code
     */
    public static function canonicalCode(string $code): string
    {
        $normalized = strtolower(trim($code));
        return static::$canonicalMap[$normalized] ?? $normalized;
    }

    private static ?Collection $memoryCache = null;

    protected static function booted()
    {
        static::saved(function ($model) {
            static::$memoryCache = null;
            Cache::forget('module_features_all');
        });

        static::deleted(function ($model) {
            static::$memoryCache = null;
            Cache::forget('module_features_all');
        });
    }

    /**
     * Clear cached module features
     */
    public static function flushCache(): void
    {
        static::$memoryCache = null;
        Cache::forget('module_features_all');
    }

    /**
     * Get all modules cached as a collection keyed by module_code
     *
     * @return Collection<string, ModuleFeature>
     */
    public static function getAllCached(): Collection
    {
        if (static::$memoryCache !== null) {
            return static::$memoryCache;
        }

        return static::$memoryCache = Cache::remember('module_features_all', 3600, function () {
            return static::all()->keyBy('module_code');
        });
    }

    /**
     * Check if a specific module is enabled.
     * Enforces:
     * 1. Canonical code always wins as sole source of truth.
     * 2. Aliases supported for backwards compatibility.
     * 3. Unknown/invalid modules FAIL CLOSED (false).
     */
    public static function isEnabled(string $moduleCode, bool $default = false): bool
    {
        $canonical = static::canonicalCode($moduleCode);
        $modules = static::getAllCached();

        $targetRow = null;
        if ($modules->has($canonical)) {
            $targetRow = $modules->get($canonical);
        } else {
            $aliases = static::$aliasMap[$canonical] ?? [];
            foreach ($aliases as $alias) {
                if ($modules->has($alias)) {
                    $targetRow = $modules->get($alias);
                    break;
                }
            }
            if (!$targetRow && $modules->has($moduleCode)) {
                $targetRow = $modules->get($moduleCode);
            }
        }

        if (!$targetRow) {
            return false;
        }

        // 1. Entitlement check (ENTITLEMENT ALWAYS WINS: if not entitled, immediately false)
        if (isset($targetRow->is_entitled) && !(bool) $targetRow->is_entitled) {
            return false;
        }

        // 2. Enabled state check
        if (!(bool) $targetRow->is_enabled) {
            return false;
        }

        // 3. Dependency check: if dependent module (e.g. face_recognition requires attendance)
        $parent = \App\Services\ModuleEntitlementService::$dependencies[$canonical] ?? null;
        if ($parent && !in_array($parent, \App\Services\ModuleEntitlementService::$coreModules, true)) {
            $parentCanonical = static::canonicalCode($parent);
            if ($parentCanonical !== $canonical) {
                $parentRow = $modules->get($parentCanonical);
                if (!$parentRow || (isset($parentRow->is_entitled) && !(bool) $parentRow->is_entitled) || !(bool) $parentRow->is_enabled) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if a module is entitled for this deployment
     */
    public static function isEntitled(string $moduleCode): bool
    {
        $canonical = static::canonicalCode($moduleCode);
        $modules = static::getAllCached();

        if ($modules->has($canonical)) {
            $row = $modules->get($canonical);
            return isset($row->is_entitled) ? (bool) $row->is_entitled : true;
        }

        $aliases = static::$aliasMap[$canonical] ?? [];
        foreach ($aliases as $alias) {
            if ($modules->has($alias)) {
                $row = $modules->get($alias);
                return isset($row->is_entitled) ? (bool) $row->is_entitled : true;
            }
        }

        return false;
    }

    /**
     * Get configuration for a module
     */
    public static function getModuleConfig(string $moduleCode, ?string $key = null, $default = null)
    {
        $modules = static::getAllCached();
        if (!$modules->has($moduleCode)) {
            $aliases = static::$aliasMap[$moduleCode] ?? [];
            foreach ($aliases as $alias) {
                if ($modules->has($alias)) {
                    $moduleCode = $alias;
                    break;
                }
            }
        }

        if (!$modules->has($moduleCode)) {
            return $default;
        }

        $config = $modules->get($moduleCode)->config ?? [];
        if ($key === null) {
            return $config;
        }

        return data_get($config, $key, $default);
    }
}
