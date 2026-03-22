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
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->text('mail_password')->nullable()->change();
        });

        Schema::table('social_login_settings', function (Blueprint $table) {
            $table->text('google_client_secret')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->string('mail_password', 255)->nullable()->change();
        });

        Schema::table('social_login_settings', function (Blueprint $table) {
            $table->string('google_client_secret', 255)->nullable()->change();
        });
    }
};
