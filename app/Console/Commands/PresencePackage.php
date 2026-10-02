<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleEntitlementService;
use App\Services\ClientPresetService;

class PresencePackage extends Command
{
    protected $signature = 'presence:package 
                            {package_code : Target package code (e.g. FNB_SMALL, FNB_STANDARD, FNB_PRO, RETAIL_SMALL, OFFICE_STANDARD, FULL_HR, CUSTOM)}
                            {--addons= : Comma-separated add-on codes (e.g. face_recognition,loans)}
                            {--preview : Preview diff without applying}
                            {--yes : Automatically confirm without interactive prompt (developer automation only)}
                            {--notes= : Audit log explanation}';

    protected $description = 'Unified developer package assignment & upgrade: sets package, entitlements, preset, validation, and locks deployment';

    public function handle(ModuleEntitlementService $service, ClientPresetService $presetService): int
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

        $defaultPreset = ModuleEntitlementService::$packageDefaultPresetMap[$targetCode] ?? 'None';

        $this->newLine();
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->line('<fg=bright-white;options=bold>   PRESENCE CANONICAL DEVELOPER PACKAGE DEPLOYMENT                      </>');
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->newLine();

        $this->line("  CURRENT PACKAGE     : <fg=yellow;options=bold>{$preview['current_package']}</>");
        $this->line("  TARGET PACKAGE      : <fg=green;options=bold>{$preview['target_package']}</>");
        $this->line("  DEFAULT PRESET      : <fg=magenta;options=bold>Preset {$defaultPreset}</>");
        $this->line("  OPERATION TYPE      : <fg=bright-white;options=bold>{$preview['action']}</>");
        $this->line("  DATA DELETION       : <fg=green;options=bold>None (All historical data 100% preserved)</>");
        $this->newLine();

        $this->line('<fg=yellow;options=bold>ENTITLEMENT DIFF:</>');
        if (!empty($preview['added_entitlements'])) {
            $this->line("  <fg=green>[+] NEW MODULES     :</> " . implode(', ', $preview['added_entitlements']));
        } else {
            $this->line("  <fg=gray>[+] NEW MODULES     : None</>");
        }
        if (!empty($preview['removed_entitlements'])) {
            $this->line("  <fg=red>[-] REMOVED ACCESS  :</> " . implode(', ', $preview['removed_entitlements']) . " <fg=gray>(Data preserved!)</>");
        } else {
            $this->line("  <fg=gray>[-] REMOVED ACCESS  : None</>");
        }
        if (!empty($preview['addons'])) {
            $this->line("  <fg=cyan>[*] ACTIVE ADD-ONS  :</> " . implode(', ', $preview['addons']));
        }
        $this->newLine();

        if ($this->option('preview')) {
            $this->info("Preview mode only. No changes applied.");
            return 0;
        }

        // Explicit confirmation required. Only --yes allowed for controlled automation.
        if (!$this->option('yes') && !$this->confirm("Are you sure you want to apply package change to {$targetCode}?", false)) {
            $this->warn("Operation cancelled.");
            return 0;
        }

        $this->info("Applying package change, entitlements, preset settings, and locking deployment...");
        $notes = $this->option('notes') ?: "Developer package assignment: {$targetCode} (Preset {$defaultPreset})";
        
        $result = $service->applyPackageChange($targetCode, $addons, null, $notes);

        if ($result['success']) {
            $this->newLine();
            $this->line('<fg=green;options=bold>========================================================================</>');
            $this->line('<fg=green;options=bold>✔ PACKAGE ASSIGNMENT COMPLETED SUCCESSFULLY!</>');
            $this->line('<fg=green;options=bold>========================================================================</>');
            $this->line("  Package      : <fg=bright-white;options=bold>{$result['package_code']}</>");
            $this->line("  Preset       : <fg=bright-white;options=bold>Preset {$defaultPreset} applied</>");
            $this->line("  Entitlements : <fg=bright-white;options=bold>Resolved & synchronized</>");
            $this->line("  Deployment   : <fg=bright-white;options=bold>LOCKED (Protected against client modification)</>");
            $this->line("  Audit Trail  : <fg=bright-white;options=bold>Recorded in deployment_package_history</>");
            $this->line("  History Data : <fg=green;options=bold>100% preserved</>");
            $this->newLine();
            return 0;
        } else {
            $this->error("Failed to apply package change.");
            return 1;
        }
    }
}
