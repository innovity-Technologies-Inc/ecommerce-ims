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
        Schema::create('wastages', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('product_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            $blueprint->integer('quantity');
            $blueprint->string('reason');
            $blueprint->foreignId('return_id')->nullable()->constrained('returns')->onDelete('set null');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wastages');
    }
};
