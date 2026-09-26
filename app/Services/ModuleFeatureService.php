<?php

namespace App\Services;

use App\Models\ModuleFeature;
use Illuminate\Support\Collection;

class ModuleFeatureService
{
    /**
     * Check if a module feature is currently enabled
     */
    public function isEnabled(string $code, bool $default = false): bool
    {
        return ModuleFeature::isEnabled($code, $default);
    }

    /**
     * Get all registered modules
     */
    public function all(): Collection
    {
        return ModuleFeature::getAllCached();
    }

    /**
     * Get all enabled modules
     */
    public function enabled(): Collection
    {
        return ModuleFeature::getAllCached()->filter(fn($m) => $m->is_enabled);
    }

    /**
     * Group modules by category
     */
    public function getByCategory(): Collection
    {
        return ModuleFeature::getAllCached()->groupBy('category');
    }

    /**
     * Enable a module (synchronizes canonical code and aliases)
     */
    public function enable(string $code): bool
    {
        $canonical = ModuleFeature::canonicalCode($code);
        $aliases = array_unique(array_merge([$code, $canonical], ModuleFeature::$aliasMap[$canonical] ?? []));
        $updated = ModuleFeature::whereIn('module_code', $aliases)->update(['is_enabled' => true]);
        ModuleFeature::flushCache();
        return $updated > 0;
    }

    /**
     * Disable a module (synchronizes canonical code and aliases)
     */
    public function disable(string $code): bool
    {
        $canonical = ModuleFeature::canonicalCode($code);
        $aliases = array_unique(array_merge([$code, $canonical], ModuleFeature::$aliasMap[$canonical] ?? []));
        $updated = ModuleFeature::whereIn('module_code', $aliases)->update(['is_enabled' => false]);
        ModuleFeature::flushCache();
        return $updated > 0;
    }

    /**
     * Toggle module status
     */
    public function toggle(string $code): bool
    {
        $canonical = ModuleFeature::canonicalCode($code);
        $current = ModuleFeature::isEnabled($canonical);
        return $current ? $this->disable($canonical) : $this->enable($canonical);
    }

    /**
     * Update module configuration
     */
    public function updateConfig(string $code, array $config): bool
    {
        $module = ModuleFeature::where('module_code', $code)->first();
        if ($module) {
            $currentConfig = $module->config ?? [];
            $module->update(['config' => array_merge($currentConfig, $config)]);
            return true;
        }
        return false;
    }
}
