<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceRequest;
use App\Http\Requests\Admin\PayslipGenerateRequest;
use App\Models\Admin;
use App\Services\HrmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class HrmController extends Controller
{
    public function __construct(
        protected HrmService $hrmService
    ) {}

    /**
     * Toggle Clock-In / Clock-Out
     */
    public function toggleAttendance(): RedirectResponse
    {
        $admin = auth('admin')->user();

        if ($admin->is_clocked_in) {
            $this->hrmService->clockOut($admin);
            $message = 'Clocked out successfully.';
        } else {
            $this->hrmService->clockIn($admin);
            $message = 'Clocked in successfully.';
        }

        return back()->with([
            'message' => $message,
            'alert-type' => 'success',
        ]);
    }

    /**
     * Display attendance list.
     */
    public function attendanceIndex(Request $request): View|string
    {
        $attendances = $this->hrmService->getAllAttendances($request->all());
        $roles = Role::all();

        if ($request->ajax()) {
            return view('admin.hrm.attendance.partials.table', compact('attendances'))->render();
        }

        return view('admin.hrm.attendance.index', compact('attendances', 'roles'));
    }

    /**
     * Show attendance create form.
     */
    public function attendanceCreate(): View
    {
        $admins = Admin::all();

        return view('admin.hrm.attendance.create', compact('admins'));
    }

    /**
     * Store manual attendance.
     */
    public function attendanceStore(AttendanceRequest $request): RedirectResponse
    {
        $this->hrmService->storeManualAttendance($request->validated());

        return redirect()->route('admin.hrm.attendance.index')->with([
            'message' => 'Attendance recorded successfully.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Display payslip list.
     */
    public function payslipIndex(Request $request): View|string
    {
        $payslips = $this->hrmService->getAllPayslips($request->all());
        $roles = Role::all();

        if ($request->ajax()) {
            return view('admin.hrm.payslip.partials.table', compact('payslips'))->render();
        }

        return view('admin.hrm.payslip.index', compact('payslips', 'roles'));
    }

    /**
     * Show payslip generate form.
     */
    public function payslipCreate(): View
    {
        $admins = Admin::all();

        return view('admin.hrm.payslip.create', compact('admins'));
    }

    /**
     * Generate payslip.
     */
    public function payslipGenerate(PayslipGenerateRequest $request): RedirectResponse
    {
        try {
            $this->hrmService->generatePayslip($request->validated());

            return redirect()->route('admin.hrm.payslip.index')->with([
                'message' => 'Payslip generated successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Show payslip details.
     */
    public function payslipShow(int $id): View
    {
        $payslip = \App\Models\Payslip::with('admin')->findOrFail($id);

        return view('admin.hrm.payslip.show', compact('payslip'));
    }

    /**
     * Update payslip status.
     */
    public function updatePayslipStatus(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
            'payment_date' => 'nullable|date',
        ]);

        $this->hrmService->updatePayslipStatus($id, $request->status, $request->payment_date);

        return back()->with([
            'message' => 'Payslip status updated successfully.',
            'alert-type' => 'success',
        ]);
    }
}
