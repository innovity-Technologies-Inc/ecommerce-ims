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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null')->after('product_variant_id');
            $table->foreignId('batch_id')->nullable()->constrained()->onDelete('set null')->after('warehouse_id');
        });

        Schema::table('batch_serials', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('set null')->after('batch_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_id');
            $table->dropConstrainedForeignId('batch_id');
        });

        Schema::table('batch_serials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_item_id');
        });
    }
};
