<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'po.view',
            'po.create',
            'po.edit',
            'po.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'admin');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'po.view',
            'po.create',
            'po.edit',
            'po.delete',
        ])->delete();
    }
};
