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
            $table->decimal('flash_discount_price', 15, 2)->default(0)->after('discount_price');
            $table->integer('flash_discount_percentage')->default(0)->after('discount_percentage');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('flash_discount_price', 15, 2)->default(0)->after('discount_price');
            $table->integer('flash_discount_percentage')->default(0)->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['flash_discount_price', 'flash_discount_percentage']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['flash_discount_price', 'flash_discount_percentage']);
        });
    }
};
