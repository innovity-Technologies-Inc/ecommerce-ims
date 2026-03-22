<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin Role
        $superAdminRole = Role::updateOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'admin']
        );

        // Create Manager Role
        $managerRole = Role::updateOrCreate(
            ['name' => 'Manager', 'guard_name' => 'admin']
        );

        // Assign all permissions to Super Admin
        $permissions = Permission::where('guard_name', 'admin')->get();
        $superAdminRole->syncPermissions($permissions);

        // Assign Super Admin role to first admin if exists
        $admin = Admin::first();
        if ($admin) {
            $admin->assignRole($superAdminRole);
        }
    }
}
