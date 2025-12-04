<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        $permissions = [
            'owner.manage.rooms',
            'owner.manage.cleaners',
            'owner.manage.issues',
            'owner.manage.moments',
            'owner.manage.capacity',
            'owner.plan',
            'owner.view.reports',
            'cleaner.view.tasks',
            'cleaner.update.tasks',
            'cleaner.report.issue',
            'admin.invite.owner',
            'admin.toggle.owner',
            'admin.link.hotel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Clear cache again after creating permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'admin.invite.owner',
            'admin.toggle.owner',
            'admin.link.hotel',
        ]);

        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->syncPermissions([
            'owner.manage.rooms',
            'owner.manage.cleaners',
            'owner.manage.issues',
            'owner.manage.moments',
            'owner.manage.capacity',
            'owner.plan',
            'owner.view.reports',
        ]);

        $cleanerRole = Role::firstOrCreate(['name' => 'cleaner']);
        $cleanerRole->syncPermissions([
            'cleaner.view.tasks',
            'cleaner.update.tasks',
            'cleaner.report.issue',
        ]);
    }
}
