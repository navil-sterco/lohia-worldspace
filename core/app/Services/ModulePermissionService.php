<?php

namespace App\Services;

use App\Models\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulePermissionService
{
    public const GUARD = 'web';

    /** Permissions for module schema CRUD (Manage Modules). */
    public const GLOBAL_PERMISSIONS = [
        'modules.view',
        'modules.create',
        'modules.update',
        'modules.delete',
    ];

    /** Per-module entry permissions (suffix after module.{slug}.). */
    public const ENTRY_ABILITIES = [
        'entries.view',
        'entries.create',
        'entries.update',
        'entries.delete',
        'entries.mapping',
        'entries.detail',
        'entries.sub-pages',
    ];

    public static function entryPermission(string $slug, string $ability): string
    {
        return "module.{$slug}.{$ability}";
    }

    public function syncGlobalPermissions(): void
    {
        foreach (self::GLOBAL_PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => self::GUARD]);
        }
    }

    public function syncModulePermissions(Module $module): void
    {
        foreach (self::ENTRY_ABILITIES as $ability) {
            $permission = Permission::firstOrCreate([
                'name' => self::entryPermission($module->slug, $ability),
                'guard_name' => self::GUARD,
            ]);

            $role = Role::findByName('Super Admin');
            $role->givePermissionTo($permission);
        }
    }

    public function renameModulePermissions(string $oldSlug, string $newSlug): void
    {
        if ($oldSlug === $newSlug) {
            return;
        }

        foreach (self::ENTRY_ABILITIES as $ability) {
            $oldName = self::entryPermission($oldSlug, $ability);
            $newName = self::entryPermission($newSlug, $ability);

            Permission::where('name', $oldName)->update(['name' => $newName]);
        }
    }

    public function deleteModulePermissions(string $slug): void
    {
        $names = array_map(
            fn(string $ability) => self::entryPermission($slug, $ability),
            self::ENTRY_ABILITIES
        );

        Permission::whereIn('name', $names)->delete();
    }

    public function syncAllModules(): int
    {
        $this->syncGlobalPermissions();

        $count = 0;
        Module::withTrashed()->each(function (Module $module) use (&$count) {
            if ($module->trashed()) {
                return;
            }
            $this->syncModulePermissions($module);
            $count++;
        });

        // Assign all permissions to role ID 1
        if ($role = Role::find(1)) {
            $role->syncPermissions(Permission::all());
        }

        return $count;
    }

    /**
     * Group permissions for the role assignment UI.
     *
     * @return array<int, array{label: string, permissions: array<int, array{id: int, name: string, label: string}>}>
     */
    public function permissionGroupsForRoles(): array
    {
        $permissions = Permission::orderBy('name')->get(['id', 'name']);

        $schemaGroup = ['label' => 'Manage Modules (schema)', 'permissions' => []];
        $otherGroup = ['label' => 'Other', 'permissions' => []];
        $moduleGroups = [];

        foreach ($permissions as $permission) {
            $item = [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => $permission->name,
            ];

            if (str_starts_with($permission->name, 'modules.')) {
                $item['label'] = str_replace('modules.', '', $permission->name);
                $schemaGroup['permissions'][] = $item;
                continue;
            }

            if (preg_match('/^module\.([^.]+)\.(.+)$/', $permission->name, $matches)) {
                $slug = $matches[1];
                $action = $matches[2];
                if (!isset($moduleGroups[$slug])) {
                    $moduleGroups[$slug] = [
                        'label' => 'Module: ' . str_replace('-', ' ', $slug),
                        'permissions' => [],
                    ];
                }
                $item['label'] = $action;
                $moduleGroups[$slug]['permissions'][] = $item;
                continue;
            }

            $otherGroup['permissions'][] = $item;
        }

        $groups = [];
        if (!empty($schemaGroup['permissions'])) {
            $groups[] = $schemaGroup;
        }
        foreach ($moduleGroups as $group) {
            if (!empty($group['permissions'])) {
                $groups[] = $group;
            }
        }
        if (!empty($otherGroup['permissions'])) {
            $groups[] = $otherGroup;
        }

        return $groups;
    }
}
