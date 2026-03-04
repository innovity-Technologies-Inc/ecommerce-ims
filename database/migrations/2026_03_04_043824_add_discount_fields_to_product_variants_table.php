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
        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'regular_price')) {
                $table->renameColumn('price', 'regular_price');
            }

            if (! Schema::hasColumn('product_variants', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('regular_price');
            }

            if (! Schema::hasColumn('product_variants', 'discount_percentage')) {
                $table->integer('discount_percentage')->nullable()->after('discount_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['discount_price', 'discount_percentage']);
        });
    }
};
