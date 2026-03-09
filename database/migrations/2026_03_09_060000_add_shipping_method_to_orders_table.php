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
            $table->foreignId('shipping_method_id')->nullable()->after('zip')->constrained()->onDelete('set null');
            $table->string('shipping_method_name')->nullable()->after('shipping_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_id']);
            $table->dropColumn(['shipping_method_id', 'shipping_method_name']);
        });
    }
};
