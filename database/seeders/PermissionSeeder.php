<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Category
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            // Brand
            'brand.view',
            'brand.create',
            'brand.edit',
            'brand.delete',

            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            // Shipping Methods
            'shipping_methods.view',
            'shipping_methods.create',
            'shipping_methods.edit',
            'shipping_methods.delete',

            // Orders
            'orders.view',
            'orders.edit', // Includes status changes

            // Returns
            'returns.view',
            'returns.edit', // Includes approval/receiving

            // Coupons
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',

            // Flash Sale
            'flash_sale.view',
            'flash_sale.edit',

            // Sliders
            'sliders.view',
            'sliders.create',
            'sliders.edit',
            'sliders.delete',

            // Homepage Sections
            'homepage_sections.view',
            'homepage_sections.edit',

            // Admin Management
            'admins.view',
            'admins.create',
            'admins.edit',
            'admins.delete',

            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Customer Management
            'customers.view',
            'customers.edit', // Includes status toggles
            'customers.delete',

            // Contact Messages
            'contact_messages.view',
            'contact_messages.delete',

            // Settings
            'settings.view',
            'settings.edit',

            // Inventory
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',

            'supplier.view',
            'supplier.create',
            'supplier.edit',
            'supplier.delete',

            'po.view',
            'po.create',
            'po.edit',
            'po.delete',

            'inventory.allocate',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'admin');
        }
    }
}
