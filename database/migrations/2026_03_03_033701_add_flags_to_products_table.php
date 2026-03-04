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
        Schema::table('products', function (Blueprint $attribute) {
            $attribute->boolean('is_new_arrival')->default(false)->after('description');
            $attribute->boolean('is_hot_deal')->default(false)->after('is_new_arrival');
            $attribute->boolean('is_featured')->default(false)->after('is_hot_deal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $attribute) {
            $attribute->dropColumn(['is_new_arrival', 'is_hot_deal', 'is_featured']);
        });
    }
};
