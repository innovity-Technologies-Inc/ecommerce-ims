<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminAttendance;
use App\Models\Payslip;
use Carbon\Carbon;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class HrmService
{
    /**
     * Get all attendance records with filtering.
     */
    public function getAllAttendances(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = AdminAttendance::with('admin');

        $filters = [];
        if (! empty($params['admin_id'])) {
            $filters['admin_id'] = $params['admin_id'];
        }
        if (! empty($params['date'])) {
            $filters['date'] = $params['date'];
        }

        // Custom filters for range
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $query->whereBetween('date', [$params['start_date'], $params['end_date']]);
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['admin.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query->latest('date')->paginate($perPage);
    }

    /**
     * Store manual attendance.
     */
    public function storeManualAttendance(array $data): AdminAttendance
    {
        $clockIn = Carbon::parse($data['clock_in']);
        $clockOut = Carbon::parse($data['clock_out']);
        $totalMinutes = $clockIn->diffInMinutes($clockOut);

        return AdminAttendance::updateOrCreate(
            ['admin_id' => $data['admin_id'], 'date' => $data['date']],
            [
                'clock_in' => $data['clock_in'],
                'clock_out' => $data['clock_out'],
                'total_minutes' => $totalMinutes,
                'is_manual' => true,
            ]
        );
    }

    /**
     * Log Clock-In (On Login).
     */
    public function logClockIn(Admin $admin): void
    {
        if (! $admin->is_time_tracking) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // Only set clock_in if it's the first login of the day
        $attendance = AdminAttendance::where('admin_id', $admin->id)
            ->where('date', $today)
            ->first();

        if (! $attendance) {
            AdminAttendance::create([
                'admin_id' => $admin->id,
                'date' => $today,
                'clock_in' => $now,
                'total_minutes' => 0,
                'is_manual' => false,
            ]);
        }
    }

    /**
     * Log Clock-Out (On Logout).
     */
    public function logClockOut(Admin $admin): void
    {
        if (! $admin->is_time_tracking) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = AdminAttendance::where('admin_id', $admin->id)
            ->where('date', $today)
            ->first();

        if ($attendance && ! $attendance->is_manual) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $totalMinutes = $clockIn->diffInMinutes($now);

            $attendance->update([
                'clock_out' => $now->toTimeString(),
                'total_minutes' => $totalMinutes,
            ]);
        }
    }

    /**
     * Get all payslips with filtering.
     */
    public function getAllPayslips(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Payslip::with('admin');

        $filters = [];
        if (! empty($params['admin_id'])) {
            $filters['admin_id'] = $params['admin_id'];
        }
        if (! empty($params['status'])) {
            $filters['status'] = $params['status'];
        }

        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $query->whereBetween('start_date', [$params['start_date'], $params['end_date']]);
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['payslip_number', 'admin.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Generate Payslip.
     */
    public function generatePayslip(array $data): Payslip
    {
        $admin = Admin::findOrFail($data['admin_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check if already exists for this exact range
        $existing = Payslip::where('admin_id', $admin->id)
            ->where('start_date', $startDate->toDateString())
            ->where('end_date', $endDate->toDateString())
            ->first();

        if ($existing) {
            throw new \Exception('Payslip already exists for this user and date range.');
        }

        // Calculate total hours from attendance in range
        $totalMinutes = AdminAttendance::where('admin_id', $admin->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('total_minutes');

        $totalHours = $totalMinutes / 60;

        // Calculate Net Salary based on salary type and range
        $netSalary = 0;
        if ($admin->salary_type === 'monthly') {
            // Prorate if not a full month
            $daysInMonth = $startDate->daysInMonth;
            $netSalary = ($admin->salary_amount / $daysInMonth) * $totalDays;
        } elseif ($admin->salary_type === 'weekly') {
            // Prorate based on 7 days
            $netSalary = ($admin->salary_amount / 7) * $totalDays;
        } elseif ($admin->salary_type === 'daily') {
            // Daily rate * days worked in range
            $daysWorked = AdminAttendance::where('admin_id', $admin->id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();
            $netSalary = $admin->salary_amount * $daysWorked;
        }

        return Payslip::create([
            'admin_id' => $admin->id,
            'payslip_number' => 'PS-'.strtoupper(Str::random(8)),
            'month' => $startDate->month,
            'year' => $startDate->year,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'salary_type' => $admin->salary_type ?? 'monthly',
            'salary_amount' => $admin->salary_amount,
            'total_hours' => $totalHours,
            'net_salary' => $netSalary,
            'status' => 'pending',
        ]);
    }

    /**
     * Update Payslip Status.
     */
    public function updatePayslipStatus(int $id, string $status, ?string $paymentDate = null): Payslip
    {
        $payslip = Payslip::findOrFail($id);
        $payslip->update([
            'status' => $status,
            'payment_date' => $status === 'paid' ? ($paymentDate ?? now()->toDateString()) : null,
        ]);

        return $payslip;
    }
}
