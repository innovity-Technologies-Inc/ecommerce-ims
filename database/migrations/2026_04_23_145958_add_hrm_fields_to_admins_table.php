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
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_time_tracking')->default(false)->after('password');
            $table->enum('salary_type', ['daily', 'weekly', 'monthly'])->nullable()->after('is_time_tracking');
            $table->decimal('salary_amount', 15, 2)->default(0)->after('salary_type');
            $table->decimal('daily_work_hours', 5, 2)->default(8)->after('salary_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['is_time_tracking', 'salary_type', 'salary_amount', 'daily_work_hours']);
        });
    }
};
