<?php

namespace App\Console\Commands;

use App\Services\EmployeeLifecycleService;
use Illuminate\Console\Command;

class CheckContractExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:check-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit employee contract expiry dates and update statuses to EXPIRING_SOON or EXPIRED';

    /**
     * Execute the console command.
     */
    public function handle(EmployeeLifecycleService $lifecycleService): int
    {
        $this->info('Starting employee contract audit...');
        $results = $lifecycleService->recheckContractStatuses();

        $this->line("  - Expired contracts updated: <comment>{$results['expired_updated']}</comment>");
        $this->line("  - Expiring soon contracts flagged: <comment>{$results['expiring_soon_updated']}</comment>");
        $this->info('Employee contract audit completed successfully.');

        return Command::SUCCESS;
    }
}
