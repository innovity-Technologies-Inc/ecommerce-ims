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
        Schema::table('general_settings', function (Blueprint $col) {
            $col->string('notify_email')->nullable()->after('currency');
        });

        Schema::table('inventory_levels', function (Blueprint $col) {
            $col->timestamp('last_alert_sent')->nullable()->after('damaged_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $col) {
            $col->dropColumn('notify_email');
        });

        Schema::table('inventory_levels', function (Blueprint $col) {
            $col->dropColumn('last_alert_sent');
        });
    }
};
