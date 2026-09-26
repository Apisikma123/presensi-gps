<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleEntitlementService;

class PresenceDeploymentLock extends Command
{
    protected $signature = 'presence:deployment-lock {--unlock : Unlock the deployment configuration profile}';
    protected $description = 'Lock or unlock the deployment configuration profile to prevent unauthorized changes';

    public function handle(ModuleEntitlementService $service): int
    {
        $unlock = $this->option('unlock');
        $service->setDeploymentLocked(!$unlock);

        if ($unlock) {
            $this->warn("Deployment configuration profile UNLOCKED for maintenance.");
        } else {
            $this->info("Deployment configuration profile LOCKED. Protected against unauthorized package changes.");
        }

        return 0;
    }
}
