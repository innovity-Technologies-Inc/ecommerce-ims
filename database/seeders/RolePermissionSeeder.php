<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin Role for admin guard
        $superAdminRole = \Spatie\Permission\Models\Role::updateOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'admin']
        );

        // Assign to first admin if exists
        $admin = Admin::first();
        if ($admin) {
            $admin->assignRole($superAdminRole);
        }
    }
}
