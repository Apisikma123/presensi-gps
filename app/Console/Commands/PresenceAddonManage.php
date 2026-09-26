<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleEntitlementService;

class PresenceAddonManage extends Command
{
    protected $signature = 'presence:addon 
                            {addon_code : Module code to add or remove (e.g. face_recognition)}
                            {--remove : Remove the specified add-on}
                            {--force : Apply without confirmation}';

    protected $description = 'Add or remove specific add-on module entitlements to the current deployment package';

    public function handle(ModuleEntitlementService $service): int
    {
        $code = strtolower(trim($this->argument('addon_code')));
        $remove = $this->option('remove');
        $action = $remove ? 'remove' : 'add';

        if (!$this->option('force') && !$this->confirm("Are you sure you want to {$action} add-on '{$code}'?", false)) {
            $this->warn("Operation cancelled.");
            return 0;
        }

        $result = $service->manageAddon($code, !$remove, null, "CLI Addon {$action} '{$code}'");

        if ($result['success']) {
            $this->info($result['message']);
            return 0;
        } else {
            $this->error("Failed to manage add-on.");
            return 1;
        }
    }
}
