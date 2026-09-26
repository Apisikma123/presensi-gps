<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleEntitlementService;

class PresencePackageInfo extends Command
{
    protected $signature = 'presence:package-info';
    protected $description = 'Display deployment package, active entitlements, and governance status';

    public function handle(ModuleEntitlementService $service): int
    {
        $pkg = $service->getCurrentPackage();
        $entitled = $service->getEntitledModules();
        $addons = $service->getActiveAddons();
        $isLocked = $service->isDeploymentLocked();

        $this->newLine();
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->line('<fg=bright-white;options=bold>   PRESENCE CLIENT PACKAGE & ENTITLEMENT GOVERNANCE STATUS              </>');
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->newLine();

        $this->line('<fg=yellow;options=bold>1. PACKAGE METADATA:</>');
        $this->line("  Active Package      : <fg=green;options=bold>{$pkg['name']} ({$pkg['code']})</>");
        $this->line("  Category            : {$pkg['category']}");
        $this->line("  Deployment Profile  : " . ($isLocked ? '<fg=green>LOCKED (Production Safe)</>' : '<fg=yellow>UNLOCKED (Maintenance)</>'));
        $this->line("  Active Add-ons      : " . (count($addons) > 0 ? implode(', ', $addons) : 'None'));
        $this->newLine();

        $this->line('<fg=yellow;options=bold>2. ENTITLEMENT SUMMARY:</>');
        $this->line("  Total Entitled Modules : <fg=bright-white;options=bold>" . count($entitled) . "</>");
        $this->line("  Included Modules       : " . implode(', ', $entitled));
        $this->newLine();

        $this->line('<fg=yellow;options=bold>3. CLIENT TOGGLEABLE PERMISSIONS:</>');
        $toggleable = [];
        foreach ($entitled as $mod) {
            if ($service->isModuleClientToggleable($mod)) {
                $toggleable[] = $mod;
            }
        }
        $this->line("  Super Admin Can Toggle : " . (count($toggleable) > 0 ? implode(', ', $toggleable) : 'None'));
        $this->newLine();
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');

        return 0;
    }
}
