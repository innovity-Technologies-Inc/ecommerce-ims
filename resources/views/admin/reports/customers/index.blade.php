@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Customer Reports & Analytics</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card no-print">
        <div class="card-body">
            <form action="{{ route('admin.reports.customers.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4 h-100" data-bs-toggle="tooltip" title="Total registered customers in the system">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-primary rounded">
                                <i class="bx bx-user fs-24 text-primary mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Customers <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($stats['total_customers']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 h-100" data-bs-toggle="tooltip" title="New customers registered within the selected period">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-success rounded">
                                <i class="bx bx-user-plus fs-24 text-success mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">New Customers <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['new_customers']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-info border-4 h-100" data-bs-toggle="tooltip" title="Customers with more than one order in the selected period">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-info rounded">
                                <i class="bx bx-refresh fs-24 text-info mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Returning <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold text-info">{{ number_format($stats['returning_customers']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4 h-100" data-bs-toggle="tooltip" title="Customers who placed an order in the last 3 months">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-warning rounded">
                                <i class="bx bx-bolt-circle fs-24 text-warning mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Active (3M) <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['active_customers']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links to Detailed Reports (2-Row Grid) -->
    <div class="row g-4 mb-4">
        <!-- Row 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 border-top border-primary border-3">
                <div class="card-body">
                    <div class="avatar-lg bg-soft-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-list-ul fs-32 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Customer List</h5>
                    <p class="text-muted small">Detailed list with order aggregates and spend analysis.</p>
                    <a href="{{ route('admin.reports.customers.list') }}" class="btn btn-soft-primary btn-sm stretched-link">View List</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 border-top border-success border-3">
                <div class="card-body">
                    <div class="avatar-lg bg-soft-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-target-lock fs-32 text-success"></i>
                    </div>
                    <h5 class="fw-bold">RFM Analysis</h5>
                    <p class="text-muted small">Segment customers by Recency, Frequency, and Monetary value.</p>
                    <a href="{{ route('admin.reports.customers.rfm') }}" class="btn btn-soft-success btn-sm stretched-link">View Analysis</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 border-top border-info border-3">
                <div class="card-body">
                    <div class="avatar-lg bg-soft-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-line-chart fs-32 text-info"></i>
                    </div>
                    <h5 class="fw-bold">Purchase Behavior</h5>
                    <p class="text-muted small">Analyze order trends, status distribution, and AOV trends.</p>
                    <a href="{{ route('admin.reports.customers.behavior') }}" class="btn btn-soft-info btn-sm stretched-link">View Behavior</a>
                </div>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 border-top border-warning border-3">
                <div class="card-body">
                    <div class="avatar-lg bg-soft-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-grid-alt fs-32 text-warning"></i>
                    </div>
                    <h5 class="fw-bold">Cohort Analysis</h5>
                    <p class="text-muted small">Track customer retention and lifecycle across signup months.</p>
                    <a href="{{ route('admin.reports.customers.cohort') }}" class="btn btn-soft-warning btn-sm stretched-link">View Cohorts</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 border-top border-danger border-3">
                <div class="card-body">
                    <div class="avatar-lg bg-soft-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-trending-up fs-32 text-danger"></i>
                    </div>
                    <h5 class="fw-bold">CLV Projections</h5>
                    <p class="text-muted small">Predictive analysis of future customer value and tiering.</p>
                    <a href="{{ route('admin.reports.customers.clv') }}" class="btn btn-soft-danger btn-sm stretched-link">View CLV</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white overflow-hidden position-relative">
                <div class="card-body d-flex flex-column justify-content-center text-center py-4">
                    <i class="bx bx-wallet position-absolute end-0 bottom-0 text-white-50 opacity-25" style="font-size: 100px; transform: translate(20%, 20%);"></i>
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2 small">Average Order Value</h6>
                    <h2 class="mb-0 display-6 fw-bold text-white">${{ number_format($stats['avg_order_value'], 2) }}</h2>
                    <p class="extra-small text-white-50 mt-3 mb-0">Calculated from all delivered orders in the selected period.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
