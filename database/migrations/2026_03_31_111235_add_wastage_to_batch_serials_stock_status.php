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
            $table->enum('stock_status', ['in_stock', 'sold', 'returned', 'wastage'])->default('in_stock')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->enum('stock_status', ['in_stock', 'sold', 'returned'])->default('in_stock')->change();
        });
    }
};
