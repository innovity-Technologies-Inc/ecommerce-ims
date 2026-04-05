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
        Schema::table('return_items', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('product_variant_id')->constrained()->onDelete('set null');
            $table->foreignId('batch_serial_id')->nullable()->after('batch_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropForeign(['batch_serial_id']);
            $table->dropColumn(['batch_id', 'batch_serial_id']);
        });
    }
};
