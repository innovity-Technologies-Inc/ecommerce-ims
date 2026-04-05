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
        Schema::table('ordered_product_batches', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['warehouse_id']);
            
            // Drop columns
            $table->dropColumn(['product_id', 'product_variant_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordered_product_batches', function (Blueprint $table) {
            $table->foreignId('product_id')->after('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->after('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('warehouse_id')->after('batch_id')->constrained()->onDelete('cascade');
        });
    }
};
