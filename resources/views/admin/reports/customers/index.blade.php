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

    <!-- Quick Links to Detailed Reports -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="card-body px-2">
                    <div class="avatar-md bg-soft-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-list-ul fs-24 text-primary"></i>
                    </div>
                    <h6 class="fw-bold">Customer List</h6>
                    <a href="{{ route('admin.reports.customers.list') }}" class="btn btn-soft-primary btn-sm stretched-link mt-2">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="card-body px-2">
                    <div class="avatar-md bg-soft-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-target-lock fs-24 text-success"></i>
                    </div>
                    <h6 class="fw-bold">RFM Analysis</h6>
                    <a href="{{ route('admin.reports.customers.rfm') }}" class="btn btn-soft-success btn-sm stretched-link mt-2">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="card-body px-2">
                    <div class="avatar-md bg-soft-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-line-chart fs-24 text-info"></i>
                    </div>
                    <h6 class="fw-bold">Behavior</h6>
                    <a href="{{ route('admin.reports.customers.behavior') }}" class="btn btn-soft-info btn-sm stretched-link mt-2">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="card-body px-2">
                    <div class="avatar-md bg-soft-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-grid-alt fs-24 text-warning"></i>
                    </div>
                    <h6 class="fw-bold">Cohorts</h6>
                    <a href="{{ route('admin.reports.customers.cohort') }}" class="btn btn-soft-warning btn-sm stretched-link mt-2">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center py-4">
                <div class="card-body px-2">
                    <div class="avatar-md bg-soft-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bx bx-trending-up fs-24 text-danger"></i>
                    </div>
                    <h6 class="fw-bold">CLV Projections</h6>
                    <a href="{{ route('admin.reports.customers.clv') }}" class="btn btn-soft-danger btn-sm stretched-link mt-2">View</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Order Value Display -->
    <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden position-relative">
        <div class="card-body py-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="text-white-50 mb-1">Average Order Value (AOV)</h5>
                    <h2 class="mb-0 display-6 fw-bold text-white">${{ number_format($stats['avg_order_value'], 2) }}</h2>
                    <p class="mb-0 mt-2 text-white-50 small">Calculated from all delivered orders in the selected period.</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="bx bx-wallet fs-100 text-white-50 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
