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
        Schema::create('return_items', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('return_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('product_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            $blueprint->integer('quantity');
            $blueprint->decimal('unit_price', 15, 2);
            $blueprint->decimal('total_price', 15, 2);
            $blueprint->enum('condition', ['pending', 'damage', 'intact'])->default('pending');
            $blueprint->boolean('is_received')->default(false);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
