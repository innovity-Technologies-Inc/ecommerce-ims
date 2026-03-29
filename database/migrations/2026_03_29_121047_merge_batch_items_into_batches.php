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
        Schema::table('batches', function (Blueprint $table) {
            $table->foreignId('product_id')->after('warehouse_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->after('product_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity')->after('product_variant_id')->default(0);
        });

        // Move existing data
        if (Schema::hasTable('batch_items')) {
            $items = DB::table('batch_items')->get();
            foreach ($items as $item) {
                // If the batch already has product_id set to null, update it.
                // Otherwise, create a new batch row with the same metadata.
                $batch = DB::table('batches')->where('id', $item->batch_id)->first();
                if ($batch && is_null($batch->product_id)) {
                    DB::table('batches')->where('id', $item->batch_id)->update([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                    ]);
                } else if ($batch) {
                    DB::table('batches')->insert([
                        'batch_number' => $batch->batch_number,
                        'purchase_order_id' => $batch->purchase_order_id,
                        'supplier_id' => $batch->supplier_id,
                        'warehouse_id' => $batch->warehouse_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ]);
                }
            }
            Schema::dropIfExists('batch_items');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_id', 'product_variant_id', 'quantity']);
        });
    }
};
