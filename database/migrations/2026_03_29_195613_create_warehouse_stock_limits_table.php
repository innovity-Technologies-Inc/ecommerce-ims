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
        Schema::create('warehouse_stock_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('min_stock')->default(0);
            $table->timestamp('last_alert_sent')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'product_variant_id', 'warehouse_id'], 'warehouse_stock_limits_unique');
        });

        // Migrate existing data from inventory_levels if any
        if (Schema::hasColumn('inventory_levels', 'min_stock_override')) {
            $levels = DB::table('inventory_levels')->whereNotNull('min_stock_override')->get();
            foreach ($levels as $level) {
                DB::table('warehouse_stock_limits')->insert([
                    'product_id' => $level->product_id,
                    'product_variant_id' => $level->product_variant_id,
                    'warehouse_id' => $level->warehouse_id,
                    'min_stock' => $level->min_stock_override,
                    'last_alert_sent' => $level->last_alert_sent,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('inventory_levels', function (Blueprint $table) {
                $table->dropColumn(['min_stock_override', 'last_alert_sent']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->integer('min_stock_override')->nullable()->after('damaged_quantity');
            $table->timestamp('last_alert_sent')->nullable()->after('min_stock_override');
        });

        Schema::dropIfExists('warehouse_stock_limits');
    }
};
