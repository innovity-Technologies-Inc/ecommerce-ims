<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\HrmExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceRequest;
use App\Http\Requests\Admin\PayslipGenerateRequest;
use App\Models\Admin;
use App\Services\HrmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
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
        $gs = \App\HelperClass::generalSettings();

        return view('admin.hrm.attendance.create', compact('admins', 'gs'));
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
     * Export attendance data.
     */
    public function attendanceExport(Request $request)
    {
        $params = $request->all();
        $attendances = $this->hrmService->getAllAttendances($params, 10000); // Higher limit for export

        $headings = ['SL', 'Employee', 'Date', 'Clock In', 'Clock Out', 'Total Hours', 'Type'];
        $exportData = [];
        $sl = 1;

        foreach ($attendances as $row) {
            $exportData[] = [
                $sl++,
                $row->admin->name ?? 'N/A',
                $row->date->format('Y-m-d'),
                $row->clock_in ?? 'N/A',
                $row->clock_out ?? 'N/A',
                number_format($row->total_minutes / 60, 2),
                $row->is_manual ? 'Manual' : 'Auto',
            ];
        }

        $title = 'Attendance Report';
        $fileName = 'attendance_report_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new HrmExport($exportData, $headings, $title), $fileName);
    }

    /**
     * Display payslip list.
     */
    public function payslipIndex(Request $request): View|string
    {
        $generations = $this->hrmService->getAllPayslipGenerations($request->all());

        if ($request->ajax()) {
            return view('admin.hrm.payslip.partials.table', compact('generations'))->render();
        }

        return view('admin.hrm.payslip.index', compact('generations'));
    }

    /**
     * Show payslip generate form.
     */
    public function payslipCreate(): View
    {
        return view('admin.hrm.payslip.create');
    }

    /**
     * Generate payslip.
     */
    public function payslipGenerate(PayslipGenerateRequest $request): RedirectResponse
    {
        try {
            $this->hrmService->generateBulkPayslips($request->validated());

            return redirect()->route('admin.hrm.payslip.index')->with([
                'message' => 'Payslips generated successfully for all employees.',
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
     * Export payslip generations.
     */
    public function payslipExport(Request $request)
    {
        $params = $request->all();
        $generations = $this->hrmService->getAllPayslipGenerations($params, 10000);

        $headings = ['SL', 'Batch Title', 'Start Date', 'End Date', 'Total Employees', 'Total Amount', 'Generated At'];
        $exportData = [];
        $sl = 1;

        foreach ($generations as $row) {
            $exportData[] = [
                $sl++,
                $row->title,
                $row->start_date->format('Y-m-d'),
                $row->end_date->format('Y-m-d'),
                $row->total_employees,
                number_format($row->total_amount, 2, '.', ''),
                $row->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $title = 'Payslip Generations Report';
        $fileName = 'payslip_generations_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new HrmExport($exportData, $headings, $title), $fileName);
    }

    /**
     * Show payslip details.
     */
    public function payslipShow(int $id): View
    {
        $generation = $this->hrmService->getPayslipGenerationDetails($id);

        return view('admin.hrm.payslip.show', compact('generation'));
    }

    /**
     * Export individual payslips from a generation batch.
     */
    public function payslipBatchExport(int $id, Request $request)
    {
        $generation = $this->hrmService->getPayslipGenerationDetails($id);

        $headings = ['SL', 'Payslip #', 'Employee', 'Period', 'Work Hours', 'Hourly Rate', 'Net Salary', 'Status', 'Payment Date'];
        $exportData = [];
        $sl = 1;

        foreach ($generation->payslips as $row) {
            $exportData[] = [
                $sl++,
                $row->payslip_number,
                $row->admin->name ?? 'N/A',
                $row->start_date->format('d M').' - '.$row->end_date->format('d M, Y'),
                number_format($row->total_hours, 2),
                number_format($row->salary_amount, 2, '.', ''),
                number_format($row->net_salary, 2, '.', ''),
                ucfirst($row->status),
                $row->payment_date ? $row->payment_date->format('Y-m-d') : 'N/A',
            ];
        }

        $title = 'Payslips - '.$generation->title;
        $fileName = 'payslips_batch_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new HrmExport($exportData, $headings, $title), $fileName);
    }

    /**
     * Show individual payslip statement for printing.
     */
    public function payslipStatement(int $id): View
    {
        $payslip = \App\Models\Payslip::with(['admin', 'generation'])->findOrFail($id);

        return view('admin.hrm.payslip.statement', compact('payslip'));
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
