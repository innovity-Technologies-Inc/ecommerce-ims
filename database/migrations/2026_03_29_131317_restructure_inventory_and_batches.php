<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create batch_products table
        Schema::create('batch_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('received_qty')->default(0);
            $table->integer('saleable_qty')->default(0);
            $table->integer('damaged_qty')->default(0);
            $table->timestamps();
        });

        // 2. Add damaged_quantity to inventory_levels
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->integer('damaged_quantity')->after('current_quantity')->default(0);
        });

        // 3. Move data from batches to batch_products (if applicable)
        // Previous structure had product_id in batches.
        $oldBatches = DB::table('batches')->whereNotNull('product_id')->get();
        foreach ($oldBatches as $oldBatch) {
            // Check if it was from a quarantine warehouse
            $warehouse = DB::table('warehouses')->where('id', $oldBatch->warehouse_id)->first();
            $isQuarantine = $warehouse && $warehouse->is_quarantine;

            DB::table('batch_products')->insert([
                'batch_id' => $oldBatch->id,
                'product_id' => $oldBatch->product_id,
                'product_variant_id' => $oldBatch->product_variant_id,
                'received_qty' => $oldBatch->quantity,
                'saleable_qty' => $isQuarantine ? 0 : $oldBatch->quantity,
                'damaged_qty' => $isQuarantine ? $oldBatch->quantity : 0,
                'created_at' => $oldBatch->created_at,
                'updated_at' => $oldBatch->updated_at,
            ]);
        }

        // 4. Restructure batches table
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_id', 'product_variant_id', 'quantity']);
            
            $table->integer('total_received_qty')->after('warehouse_id')->default(0);
            $table->integer('total_saleable_qty')->after('total_received_qty')->default(0);
            $table->integer('total_damaged_qty')->after('total_saleable_qty')->default(0);
        });

        // 5. Update batch_serials table
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->renameColumn('status', 'product_status');
        });

        // Update product_status values (mapping old statuses to new)
        DB::table('batch_serials')->where('product_status', 'in-stock')->update(['product_status' => 'good']);
        // 'damaged' remains 'damaged'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->renameColumn('product_status', 'status');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['total_received_qty', 'total_saleable_qty', 'total_damaged_qty']);
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity')->default(0);
        });

        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->dropColumn('damaged_quantity');
        });

        Schema::dropIfExists('batch_products');
    }
};
