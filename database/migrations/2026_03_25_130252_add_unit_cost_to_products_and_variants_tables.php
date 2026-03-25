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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->default(0)->after('regular_price');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->default(0)->after('regular_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};
