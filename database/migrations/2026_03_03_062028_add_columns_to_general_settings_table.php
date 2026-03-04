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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('dark_logo')->nullable()->after('business_name');
            $table->string('light_logo')->nullable()->after('dark_logo');
            $table->string('breadcrumb_image')->nullable()->after('light_logo');
            $table->string('meta_title')->nullable()->after('breadcrumb_image');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('favicon')->nullable()->after('meta_description');
            $table->string('currency')->nullable()->after('favicon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'dark_logo',
                'light_logo',
                'breadcrumb_image',
                'meta_title',
                'meta_description',
                'favicon',
                'currency',
            ]);
        });
    }
};
