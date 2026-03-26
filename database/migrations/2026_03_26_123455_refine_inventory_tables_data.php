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
        // 1. Update batches table
        Schema::table('batches', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('purchase_order_id')->constrained('suppliers')->onDelete('set null');
        });

        // 2. Update batch_serials table
        // Since SQLite/some DBs don't support modify enum easily, we'll handle it carefully
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->string('status')->default('in-stock')->change();
        });

        // 3. Update stock_ledgers table
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('warehouse_id')->constrained('suppliers')->onDelete('set null');
            $table->decimal('unit_cost', 15, 2)->default(0)->after('batch_id');
            $table->decimal('cost', 15, 2)->default(0)->after('unit_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });

        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'unit_cost', 'cost']);
        });
    }
};
