<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'admin';

        // Define  permissions
        $permissionsMap = [
            'devotionals' => ['view', 'view own', 'create', 'edit', 'edit own', 'delete', 'publish', 'unpublish', 'submit for review'],
            'users' => ['view', 'create', 'edit', 'delete', 'ban', 'unban'],
            'admins' => ['view', 'create', 'edit', 'delete', 'assign roles'],
            'roles' => ['view', 'create', 'edit', 'delete', 'assign permissions'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'notes' => ['view', 'view all', 'delete'],
            'prayer-notes' => ['view', 'view all', 'delete'],
            'memory-verses' => ['view', 'view all', 'delete'],

            // add other modules freely
            'settings' => ['view', 'update'],
            'dashboard' => ['view'],
        ];

        foreach ($permissionsMap as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$resource}.{$action}",
                    'guard_name' => $guard,
                ]);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guard]);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => $guard]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', $guard)->pluck('name')->toArray()
        );

        // ✅ editor gets limited permissions
        $editor->syncPermissions([
            'dashboard.view',
            'devotionals.view',
            'devotionals.view own',
            'devotionals.create',
            'devotionals.edit own',
            'devotionals.submit for review',
        ]);

    }
}
