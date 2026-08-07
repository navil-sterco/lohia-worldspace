<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-page',
            'create-page',
            'edit-page',
            'delete-page',
            'view-page-section',
            'create-page-section',
            'edit-page-section',
            'delete-page-section',
            'view-tab',
            'create-tab',
            'edit-tab',
            'delete-tab',
            'view-gallery',
            'create-gallery',
            'edit-gallery',
            'delete-gallery',
            'view-menu',
            'create-menu',
            'edit-menu',
            'delete-menu',
            'view-contact',
            'create-contact',
            'edit-contact',
            'delete-contact',
            'view-program-form',
            'create-program-form',
            'edit-program-form',
            'delete-program-form',
            'view-support',
            'create-support',
            'edit-support',
            'delete-support',
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permission',
            'create-permission',
            'edit-permission',
            'delete-permission',
            'view-seo',
            'create-seo',
            'edit-seo',
            'delete-seo',
            'view-section',
            'create-section',
            'delete-section',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
