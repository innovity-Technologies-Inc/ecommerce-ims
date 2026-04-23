<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminAttendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = Admin::all();

        foreach ($admins as $admin) {
            // Set default salary if not set (for demo)
            if ($admin->salary_amount == 0) {
                $admin->update([
                    'is_time_tracking' => true,
                    'salary_amount' => 15.00, // $15 per hour
                    'daily_work_hours' => 8.0,
                ]);
            }

            // Generate for last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::today()->subDays($i);

                // Skip weekends for realism
                if ($date->isWeekend()) {
                    continue;
                }

                // Random clock-in between 8:00 AM and 10:00 AM
                $clockIn = $date->copy()->hour(rand(8, 9))->minute(rand(0, 59));
                
                // Random clock-out between 4:00 PM and 6:00 PM
                $clockOut = $date->copy()->hour(rand(16, 17))->minute(rand(0, 59));
                
                $totalMinutes = $clockIn->diffInMinutes($clockOut);

                AdminAttendance::updateOrCreate(
                    ['admin_id' => $admin->id, 'date' => $date->toDateString()],
                    [
                        'clock_in' => $clockIn->toTimeString(),
                        'clock_out' => $clockOut->toTimeString(),
                        'total_minutes' => $totalMinutes,
                        'is_manual' => false,
                    ]
                );
            }
        }
    }
}
