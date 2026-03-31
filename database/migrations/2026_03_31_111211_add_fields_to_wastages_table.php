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
        Schema::table('wastages', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('product_variant_id')->constrained('warehouses')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->after('warehouse_id')->constrained('batches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->after('return_id')->constrained('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wastages', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['batch_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['warehouse_id', 'batch_id', 'created_by']);
        });
    }
};
