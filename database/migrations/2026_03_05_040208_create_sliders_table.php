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
        Schema::create('sliders', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('image');
            $blueprint->string('title');
            $blueprint->string('subtitle')->nullable();
            $blueprint->string('subtext')->nullable();
            $blueprint->string('button_name')->nullable();
            $blueprint->string('button_url')->nullable();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->integer('position')->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
