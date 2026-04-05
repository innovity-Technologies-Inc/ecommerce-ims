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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('product_discount', 15, 2)->default(0)->after('discount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('regular_price', 15, 2)->default(0)->after('variant_name');
            $table->decimal('product_discount', 15, 2)->default(0)->after('regular_price');
            $table->decimal('coupon_discount', 15, 2)->default(0)->after('product_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('product_discount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['regular_price', 'product_discount', 'coupon_discount']);
        });
    }
};
