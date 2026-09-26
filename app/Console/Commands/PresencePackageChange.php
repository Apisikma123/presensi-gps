<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleEntitlementService;

class PresencePackageChange extends Command
{
    protected $signature = 'presence:package-change 
                            {package_code : Target package code (e.g. FNB_SMALL, FNB_PRO, FULL_HR, RETAIL_SMALL, OFFICE_STANDARD)}
                            {--addons= : Comma-separated add-on codes (e.g. face_recognition,loans)}
                            {--preview : Preview diff without applying}
                            {--force : Force apply without interactive prompt}
                            {--notes= : Audit log explanation}';

    protected $description = 'Safely upgrade or downgrade deployment package with diff preview and zero data loss';

    public function handle(ModuleEntitlementService $service): int
    {
        $targetCode = strtoupper(trim($this->argument('package_code')));
        $addonsRaw = $this->option('addons');
        $addons = $addonsRaw ? array_map('trim', explode(',', $addonsRaw)) : [];

        try {
            $preview = $service->previewPackageChange($targetCode, $addons);
        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->line('<fg=bright-white;options=bold>   PRESENCE CONTROLLED PACKAGE CHANGE PREVIEW                           </>');
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->newLine();

        $this->line("  Current Package     : <fg=yellow;options=bold>{$preview['current_package']}</>");
        $this->line("  Target Package      : <fg=green;options=bold>{$preview['target_package']}</>");
        $this->line("  Operation Type      : <fg=bright-white;options=bold>{$preview['action']}</>");
        $this->line("  Data Impact         : <fg=green;options=bold>{$preview['data_impact']}</>");
        $this->newLine();

        $this->line('<fg=yellow;options=bold>ENTITLEMENT DIFF:</>');
        if (!empty($preview['added_entitlements'])) {
            $this->line("  <fg=green>[+] NEWLY ENTITLED  :</> " . implode(', ', $preview['added_entitlements']));
        }
        if (!empty($preview['removed_entitlements'])) {
            $this->line("  <fg=red>[-] REMOVED ACCESS  :</> " . implode(', ', $preview['removed_entitlements']) . " <fg=gray>(Data preserved!)</>");
        }
        if (!empty($preview['addons'])) {
            $this->line("  <fg=cyan>[*] ACTIVE ADD-ONS  :</> " . implode(', ', $preview['addons']));
        }
        $this->newLine();

        if ($this->option('preview')) {
            $this->info("Preview mode only. No changes applied.");
            return 0;
        }

        if (!$this->option('force') && !$this->confirm("Are you sure you want to apply package change to {$targetCode}?", false)) {
            $this->warn("Operation cancelled.");
            return 0;
        }

        $this->info("Applying package change transactionally...");
        $notes = $this->option('notes') ?: "CLI Package change by deployment administrator to {$targetCode}";
        $result = $service->applyPackageChange($targetCode, $addons, null, $notes);

        if ($result['success']) {
            $this->newLine();
            $this->line('<fg=green;options=bold>✔ PACKAGE UPGRADE/DOWNGRADE APPLIED SUCCESSFULLY!</>');
            $this->line("  Active Package: {$result['package_code']}");
            $this->line("  Caches flushed. All historical data 100% preserved.");
            $this->newLine();
            return 0;
        } else {
            $this->error("Failed to apply package change.");
            return 1;
        }
    }
}
