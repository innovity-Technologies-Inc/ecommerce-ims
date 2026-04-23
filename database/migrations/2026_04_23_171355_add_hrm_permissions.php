<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Permissions
        $permissions = [
            'hrm.view',
            'hrm.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'admin');
        }

        // 2. Assign to all roles (Super Admin etc)
        $roles = Role::where('guard_name', 'admin')->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', ['hrm.view', 'hrm.edit'])->where('guard_name', 'admin')->delete();
    }
};
