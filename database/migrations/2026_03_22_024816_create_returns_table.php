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
        Schema::create('returns', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('order_id')->constrained()->onDelete('cascade');
            $blueprint->string('return_id')->unique();
            $blueprint->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $blueprint->text('reason');
            $blueprint->enum('status', ['pending', 'approved', 'rejected', 'received'])->default('pending');
            $blueprint->text('rejection_reason')->nullable();
            $blueprint->string('image')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
