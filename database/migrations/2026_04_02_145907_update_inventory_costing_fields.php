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
        // 1. Update stock_ledgers: remove unit_cost and cost
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropColumn(['unit_cost', 'cost']);
        });

        // 2. Update batch_products: add unit_cost
        Schema::table('batch_products', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->after('product_variant_id')->default(0.00);
        });

        // 3. Update products: remove unit_cost
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit_cost']);
        });

        // 4. Update product_variants: remove unit_cost
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['unit_cost']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add unit_cost and cost to stock_ledgers
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->after('batch_id')->default(0.00);
            $table->decimal('cost', 15, 2)->after('unit_cost')->default(0.00);
        });

        // Remove unit_cost from batch_products
        Schema::table('batch_products', function (Blueprint $table) {
            $table->dropColumn(['unit_cost']);
        });

        // Re-add unit_cost to products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->after('regular_price')->default(0.00);
        });

        // Re-add unit_cost to product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->after('regular_price')->default(0.00);
        });
    }
};
