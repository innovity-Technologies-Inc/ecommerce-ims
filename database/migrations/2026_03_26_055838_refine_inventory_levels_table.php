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
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('product_variant_id')->constrained('batches')->onDelete('cascade');
            $table->renameColumn('quantity', 'current_quantity');
        });

        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->integer('min_stock_override')->nullable()->after('current_quantity');
            $table->timestamp('last_alert_sent')->nullable()->after('min_stock_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['batch_id', 'min_stock_override', 'last_alert_sent']);
            $table->renameColumn('current_quantity', 'quantity');
        });
    }
};
