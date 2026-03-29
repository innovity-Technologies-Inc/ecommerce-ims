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
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->enum('product_status', ['good', 'damaged', 'damaged_return'])->default('good')->change();
            $table->enum('stock_status', ['in_stock', 'sold'])->default('in_stock')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->string('product_status')->default('good')->change();
            $table->string('stock_status')->default('in_stock')->change();
        });
    }
};
