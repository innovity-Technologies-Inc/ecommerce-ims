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
            $table->enum('min_stock_type', ['global', 'warehouse'])->default('global')->after('min_stock_global');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('min_stock_global')->default(0)->after('stock');
            $table->enum('min_stock_type', ['global', 'warehouse'])->default('global')->after('min_stock_global');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('min_stock_type');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['min_stock_global', 'min_stock_type']);
        });
    }
};
