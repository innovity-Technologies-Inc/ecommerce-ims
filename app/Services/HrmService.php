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
        if (! empty($params['date'])) {
            $filters['date'] = $params['date'];
        }

        // Role filtering
        if (! empty($params['role_id'])) {
            $query->whereHas('admin', function ($q) use ($params) {
                $q->whereHas('roles', function ($rq) use ($params) {
                    $rq->where('id', $params['role_id']);
                });
            });
        }

        // Custom filters for range
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $query->whereBetween('date', [$params['start_date'], $params['end_date']]);
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['admin.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Sorting
        $sort = $params['sort'] ?? 'latest';
        if ($sort === 'latest') {
            $query->latest('date')->latest('created_at');
        } elseif ($sort === 'oldest') {
            $query->oldest('date')->oldest('created_at');
        } elseif ($sort === 'name_asc') {
            $query->join('admins', 'admin_attendances.admin_id', '=', 'admins.id')
                ->orderBy('admins.name', 'asc')
                ->select('admin_attendances.*');
        } elseif ($sort === 'name_desc') {
            $query->join('admins', 'admin_attendances.admin_id', '=', 'admins.id')
                ->orderBy('admins.name', 'desc')
                ->select('admin_attendances.*');
        }

        return $query->paginate($perPage);
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
     * Manual Clock-In via Button
     */
    public function clockIn(Admin $admin): void
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Update/Create Today's Attendance Record
        $attendance = AdminAttendance::where('admin_id', $admin->id)
            ->where('date', $today)
            ->first();

        if (! $attendance) {
            AdminAttendance::create([
                'admin_id' => $admin->id,
                'date' => $today,
                'clock_in' => $now->toTimeString(),
                'total_minutes' => 0,
                'is_manual' => false,
            ]);
        }

        // 2. Update Admin status and session start
        $admin->update([
            'is_clocked_in' => true,
            'last_login_at' => $now,
        ]);
    }

    /**
     * Manual Clock-Out via Button
     */
    public function clockOut(Admin $admin): void
    {
        if (! $admin->last_login_at) {
            $admin->update(['is_clocked_in' => false]);

            return;
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = AdminAttendance::where('admin_id', $admin->id)
            ->where('date', $today)
            ->first();

        if ($attendance && ! $attendance->is_manual) {
            // Calculate minutes for this session
            $sessionStart = Carbon::parse($admin->last_login_at);
            $sessionMinutes = $sessionStart->diffInMinutes($now);

            // Accumulate time
            $attendance->increment('total_minutes', $sessionMinutes);

            // Update final clock_out for display
            $attendance->update([
                'clock_out' => $now->toTimeString(),
            ]);
        }

        // Reset status
        $admin->update([
            'is_clocked_in' => false,
            'last_login_at' => null,
        ]);
    }

    /**
     * Get all payslips with filtering.
     */
    public function getAllPayslips(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Payslip::with('admin');

        $filters = [];
        if (! empty($params['status'])) {
            $filters['status'] = $params['status'];
        }

        // Role filtering
        if (! empty($params['role_id'])) {
            $query->whereHas('admin', function ($q) use ($params) {
                $q->whereHas('roles', function ($rq) use ($params) {
                    $rq->where('id', $params['role_id']);
                });
            });
        }

        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $query->whereBetween('start_date', [$params['start_date'], $params['end_date']]);
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['payslip_number', 'admin.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Sorting
        $sort = $params['sort'] ?? 'latest';
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name_asc') {
            $query->join('admins', 'payslips.admin_id', '=', 'admins.id')
                ->orderBy('admins.name', 'asc')
                ->select('payslips.*');
        } elseif ($sort === 'name_desc') {
            $query->join('admins', 'payslips.admin_id', '=', 'admins.id')
                ->orderBy('admins.name', 'desc')
                ->select('payslips.*');
        }

        return $query->paginate($perPage);
    }

    /**
     * Generate Payslip.
     */
    public function generatePayslip(array $data): Payslip
    {
        $admin = Admin::findOrFail($data['admin_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

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

        /**
         * Logic: Direct Hourly Rate based Calculation
         * Formula: Hourly Salary Rate * Actual Hours Worked
         */
        $hourlyRate = $admin->salary_amount ?? 0;
        $netSalary = $hourlyRate * $totalHours;

        return Payslip::create([
            'admin_id' => $admin->id,
            'payslip_number' => 'PS-'.strtoupper(Str::random(8)),
            'month' => $startDate->month,
            'year' => $startDate->year,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'salary_type' => 'daily', // Implicitly hours based
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
