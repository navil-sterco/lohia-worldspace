<?php

namespace App\Observers;

use App\Models\Module;
use App\Services\ModulePermissionService;

class ModuleObserver
{
    public function __construct(
        private ModulePermissionService $modulePermissions
    ) {}

    public function created(Module $module): void
    {
        $this->modulePermissions->syncModulePermissions($module);
    }

    public function updated(Module $module): void
    {
        if ($module->wasChanged('slug')) {
            $this->modulePermissions->renameModulePermissions(
                $module->getOriginal('slug'),
                $module->slug
            );
        }
    }

    public function deleted(Module $module): void
    {
        $this->modulePermissions->deleteModulePermissions($module->slug);
    }
}
