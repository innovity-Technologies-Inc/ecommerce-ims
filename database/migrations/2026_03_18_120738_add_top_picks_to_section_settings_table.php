<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('section_settings')->insert([
            'section_name' => 'top_picks',
            'section_title' => 'Top Picks',
            'section_subtitle' => 'Our highly recommended products for you',
            'mode' => 'organic',
            'limit' => 8,
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('section_settings')->where('section_name', 'top_picks')->delete();
    }
};
