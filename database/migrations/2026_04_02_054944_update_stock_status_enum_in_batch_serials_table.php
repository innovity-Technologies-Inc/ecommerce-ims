<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE batch_serials MODIFY COLUMN stock_status ENUM('in_stock', 'shipped', 'sold', 'returned', 'wastage') NOT NULL DEFAULT 'in_stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE batch_serials MODIFY COLUMN stock_status ENUM('in_stock', 'sold', 'returned', 'wastage') NOT NULL DEFAULT 'in_stock'");
    }
};
