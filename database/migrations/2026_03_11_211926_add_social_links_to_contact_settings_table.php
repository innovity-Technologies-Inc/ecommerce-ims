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
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->string('facebook_url')->nullable();
            $table->boolean('facebook_status')->default(0);

            $table->string('instagram_url')->nullable();
            $table->boolean('instagram_status')->default(0);

            $table->string('tiktok_url')->nullable();
            $table->boolean('tiktok_status')->default(0);

            $table->string('x_url')->nullable();
            $table->boolean('x_status')->default(0);

            $table->string('thread_url')->nullable();
            $table->boolean('thread_status')->default(0);

            $table->string('linkedin_url')->nullable();
            $table->boolean('linkedin_status')->default(0);

            $table->string('whatsapp_url')->nullable();
            $table->boolean('whatsapp_status')->default(0);

            $table->string('youtube_url')->nullable();
            $table->boolean('youtube_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url', 'facebook_status',
                'instagram_url', 'instagram_status',
                'tiktok_url', 'tiktok_status',
                'x_url', 'x_status',
                'thread_url', 'thread_status',
                'linkedin_url', 'linkedin_status',
                'whatsapp_url', 'whatsapp_status',
                'youtube_url', 'youtube_status',
            ]);
        });
    }
};
