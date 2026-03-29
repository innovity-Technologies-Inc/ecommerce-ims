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
        Schema::table('stock_ledgers', function (Blueprint $table) {
            // First drop the column (this also drops the primary key in MySQL)
            $table->dropColumn('id');
        });

        Schema::table('stock_ledgers', function (Blueprint $table) {
            // Add the new auto-incrementing id as the first column
            $table->id()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
        });
    }
};
