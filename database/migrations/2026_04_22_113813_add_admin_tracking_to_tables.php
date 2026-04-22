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
            Schema::table($tableName, function (Blueprint $table) {
                // Drop foreign keys first if they exist
                // Note: purchase_orders, stock_adjustments, wastages already had created_by
                if ($table->getTable() !== 'purchase_orders' &&
                    $table->getTable() !== 'stock_adjustments' &&
                    $table->getTable() !== 'wastages') {
                    $table->dropForeign([$table->getTable().'_created_by_foreign']);
                    $table->dropColumn('created_by');
                }

                $table->dropForeign([$table->getTable().'_updated_by_foreign']);
                $table->dropColumn('updated_by');
            });
        }
    }
};
