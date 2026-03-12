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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('apply_for', ['total_product_price', 'shipping_cost'])->default('total_product_price');
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable(); // Relevant for percentage discount
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('active_on');
            $table->date('expired_on');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
