<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerReportController extends Controller
{
    public function __construct(
        protected CustomerReportService $customerReportService
    ) {}

    /**
     * Display the customer reports overview dashboard
     */
    public function index(Request $request): View
    {
        $filters = $request->all();
        $stats = $this->customerReportService->getOverviewStats($filters);

        return view('admin.reports.customers.index', compact('stats', 'filters'));
    }

    /**
     * Display a filterable list of customers with order aggregates
     */
    public function list(Request $request): View
    {
        $filters = $request->all();
        $customers = $this->customerReportService->getCustomerList($filters);

        return view('admin.reports.customers.list', compact('customers', 'filters'));
    }

    /**
     * Display RFM Analysis
     */
    public function rfm(): View
    {
        $rfm = $this->customerReportService->getRFMAnalysis();

        return view('admin.reports.customers.rfm', compact('rfm'));
    }

    /**
     * Display Purchase Behavior Analytics
     */
    public function behavior(Request $request): View
    {
        $filters = $request->all();
        $behavior = $this->customerReportService->getPurchaseBehavior($filters);

        return view('admin.reports.customers.behavior', compact('behavior', 'filters'));
    }

    /**
     * Display Cohort Analysis
     */
    public function cohort(): View
    {
        $cohorts = $this->customerReportService->getCohortAnalysis();
        return view('admin.reports.customers.cohort', compact('cohorts'));
    }

    /**
     * Display CLV Projections
     */
    public function clv(Request $request): View
    {
        $filters = $request->all();
        $clv = $this->customerReportService->getCLVProjections($filters);
        return view('admin.reports.customers.clv', compact('clv', 'filters'));
    }
}
