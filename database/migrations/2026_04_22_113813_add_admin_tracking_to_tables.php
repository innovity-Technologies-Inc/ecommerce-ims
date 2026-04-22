<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'brands', 'categories', 'products', 'product_variants', 'product_images',
            'coupons', 'shipping_methods', 'warehouses', 'suppliers', 'batches',
            'inventory_levels', 'purchase_orders', 'purchase_order_items', 'orders',
            'order_items', 'order_status_logs', 'ordered_product_batches', 'returns',
            'return_items', 'return_images', 'rma_items', 'supplier_rmas',
            'stock_adjustments', 'stock_adjustment_items', 'stock_ledgers',
            'section_settings', 'section_product', 'policy_settings', 'faqs',
            'admin_notifications', 'wastages',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableLayout) use ($table) {
                $columns = Schema::getColumnListing($table);

                if (! in_array('created_by', $columns)) {
                    $tableLayout->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
                }

                if (! in_array('updated_by', $columns)) {
                    $tableLayout->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'brands', 'categories', 'products', 'product_variants', 'product_images',
            'coupons', 'shipping_methods', 'warehouses', 'suppliers', 'batches',
            'inventory_levels', 'purchase_orders', 'purchase_order_items', 'orders',
            'order_items', 'order_status_logs', 'ordered_product_batches', 'returns',
            'return_items', 'return_images', 'rma_items', 'supplier_rmas',
            'stock_adjustments', 'stock_adjustment_items', 'stock_ledgers',
            'section_settings', 'section_product', 'policy_settings', 'faqs',
            'admin_notifications', 'wastages',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = Schema::getColumnListing($tableName);

                // Note: purchase_orders, stock_adjustments, wastages already had created_by
                if (! in_array($tableName, ['purchase_orders', 'stock_adjustments', 'wastages'])) {
                    if (in_array('created_by', $columns)) {
                        $table->dropForeign(['created_by']);
                        $table->dropColumn('created_by');
                    }
                }

                if (in_array('updated_by', $columns)) {
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn('updated_by');
                }
            });
        }
    }
};
