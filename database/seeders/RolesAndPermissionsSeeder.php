<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolesAndPermissionsSeeder extends Seeder {
  public function run(): void {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    foreach ([
      'owner.manage.rooms','owner.manage.cleaners','owner.manage.issues','owner.manage.moments',
      'owner.manage.capacity','owner.plan','owner.view.reports',
      'cleaner.view.tasks','cleaner.update.tasks','cleaner.report.issue',
      'admin.invite.owner','admin.toggle.owner','admin.link.hotel',
    ] as $p) { Permission::findOrCreate($p); }
    Role::findOrCreate('admin')->givePermissionTo(['admin.invite.owner','admin.toggle.owner','admin.link.hotel']);
    Role::findOrCreate('owner')->givePermissionTo([
      'owner.manage.rooms','owner.manage.cleaners','owner.manage.issues','owner.manage.moments',
      'owner.manage.capacity','owner.plan','owner.view.reports'
    ]);
    Role::findOrCreate('cleaner')->givePermissionTo(['cleaner.view.tasks','cleaner.update.tasks','cleaner.report.issue']);
  }
}
