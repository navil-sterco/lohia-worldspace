<?php

namespace App\Console\Commands;

use App\Services\ModulePermissionService;
use Illuminate\Console\Command;

class SyncModulePermissions extends Command
{
    protected $signature = 'modules:sync-permissions';

    protected $description = 'Create global module permissions and per-module entry permissions for all modules';

    public function handle(ModulePermissionService $service): int
    {
        $service->syncGlobalPermissions();
        $this->info('Global module permissions synced.');

        $count = $service->syncAllModules();
        $this->info("Synced entry permissions for {$count} module(s).");

        return self::SUCCESS;
    }
}
