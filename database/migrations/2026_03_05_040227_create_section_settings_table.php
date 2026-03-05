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
        Schema::create('section_settings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('section_name')->unique();
            $blueprint->string('section_title')->nullable();
            $blueprint->enum('mode', ['organic', 'custom'])->default('organic');
            $blueprint->integer('limit')->default(10);
            $blueprint->boolean('is_visible')->default(true);
            $blueprint->timestamps();
        });

        Schema::create('section_product', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('section_setting_id')->constrained('section_settings')->onDelete('cascade');
            $blueprint->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $blueprint->integer('position')->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_product');
        Schema::dropIfExists('section_settings');
    }
};
