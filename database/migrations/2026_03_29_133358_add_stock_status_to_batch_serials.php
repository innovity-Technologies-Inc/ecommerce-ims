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
            $table->string('stock_status')->after('product_status')->default('in_stock')->comment('in_stock, sold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_serials', function (Blueprint $table) {
            $table->dropColumn('stock_status');
        });
    }
};
