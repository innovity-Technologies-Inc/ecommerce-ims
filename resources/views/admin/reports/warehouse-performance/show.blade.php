@extends('admin.structure.app')

@section('content')
<div class="container-xxl" id="performance-detail-report">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print-section">
        <div>
            <a href="{{ route('admin.reports.warehouse-performance', ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bx bx-arrow-back"></i> Back to Dashboard
            </a>
            <h4 class="mb-0">Warehouse: {{ $warehouse->name }}</h4>
            <p class="text-muted small mb-0">Performance Period: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</p>
        </div>
    </div>


    <div class="row g-4 mb-4">
        <!-- Stock Balance Card -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0">Stock Reconciliation</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4 text-center">
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-semibold">Opening Stock</label>
                            <h3 class="fw-bold mb-0">{{ number_format($report['opening_stock']) }}</h3>
                        </div>
                        <div class="col-md-1 text-center d-none d-md-flex align-items-center justify-content-center">
                            <i class="bx bx-plus text-muted fs-4"></i>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-semibold">Total Inflows</label>
                            <h3 class="fw-bold text-success mb-0">+{{ number_format($report['received_qty'] + $report['returns_qty'] + $report['adjusted_in'] + $report['damaged_plus_stock']) }}</h3>
                            <div class="small text-muted mt-1">
                                Rcv: {{ $report['received_qty'] }} | Dmg: {{ $report['damaged_plus_stock'] }} | Ret: {{ $report['returns_qty'] }} | Adj: {{ $report['adjusted_in'] }}
                            </div>
                        </div>
                        <div class="col-md-1 text-center d-none d-md-flex align-items-center justify-content-center">
                            <i class="bx bx-minus text-muted fs-4"></i>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-semibold">Total Outflows</label>
                            <h3 class="fw-bold text-danger mb-0">-{{ number_format($report['sold_qty'] + $report['rtv_qty'] + $report['wastage_entry_qty']) }}</h3>
                            <div class="small text-muted mt-1">
                                Sold: {{ $report['sold_qty'] }} | RTV: {{ $report['rtv_qty'] }} | Wst: {{ $report['wastage_entry_qty'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side-by-Side Composition & Snapshot -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                        <div class="card-body p-3">
                            <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Stock Composition</label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary"><i class="bx bxs-circle text-success me-1 small"></i> Saleable:</span>
                                <span class="fw-bold h5 mb-0">{{ number_format($report['saleable_closing']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary"><i class="bx bxs-circle text-danger me-1 small"></i> Damaged:</span>
                                <span class="fw-bold h5 mb-0">{{ number_format($report['damaged_closing']) }}</span>
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                <span class="small text-muted italic">Internal Wastage: {{ $report['wastage_entry_qty'] }} units</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 bg-dark text-white">
                        <div class="card-body p-3 d-flex flex-column justify-content-center">
                            <label class="text-white-50 small text-uppercase fw-bold mb-1">Live Warehouse Snapshot</label>
                            <div class="d-flex align-items-center justify-content-between">
                                <h2 class="fw-bold mb-0 text-white">{{ number_format($report['total_closing_stock']) }}</h2>
                                <div class="bg-white bg-opacity-10 p-2 rounded">
                                    <i class="bx bx-package fs-3 text-white"></i>
                                </div>
                            </div>
                            <span class="small text-white-50">Total Physical Units On-Hand</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valuation Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0 bg-soft-primary p-3 rounded-circle me-3">
                            <i class="bx bx-wallet text-primary fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-1">Inventory Valuation</h6>
                            <h2 class="fw-bold text-dark mb-0">${{ number_format($report['inventory_value'], 2) }}</h2>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="bg-light p-3 rounded mb-3 border-start border-3 border-info shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold">Stock Turnover</span>
                                <span class="badge bg-info text-white">{{ number_format($report['stock_turnover'], 2) }}x</span>
                            </div>
                            <div class="progress bg-white" style="height: 10px; border: 1px solid #dee2e6;">
                                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ min($report['stock_turnover'] * 20, 100) }}%"></div>
                            </div>
                            <div class="mt-1">
                                <small class="text-muted x-small">Efficiency Target: 1.0x or higher</small>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded border-start border-3 border-{{ $report['low_stock_count'] > 0 ? 'danger' : 'success' }} shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Low Stock SKUs</span>
                                <span class="h5 fw-bold mb-0 text-{{ $report['low_stock_count'] > 0 ? 'danger' : 'success' }}">
                                    {{ $report['low_stock_count'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center border-top pt-3">
                        <p class="text-muted small mb-0">
                            <i class="bx bx-info-circle me-1"></i> Based on Procurement Cost
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fulfillment Metrics -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0">Fulfillment Efficiency</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6 text-center border-end">
                            <h1 class="display-5 fw-bold text-success mb-0">{{ number_format($report['net_fill_rate'], 1) }}%</h1>
                            <span class="text-muted small text-uppercase">Net Fill Rate</span>
                            <div class="progress mt-2 mx-auto" style="height: 6px; width: 80%;">
                                <div class="progress-bar bg-success" style="width: {{ $report['net_fill_rate'] }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-6 ps-4">
                            <div class="mb-2">
                                <label class="text-muted small">Gross Fill Rate:</label>
                                <span class="fw-bold">{{ number_format($report['fill_rate'], 1) }}%</span>
                            </div>
                            <div class="mb-2">
                                <label class="text-muted small">Return Rate:</label>
                                <span class="fw-bold text-danger">{{ number_format($report['return_rate'], 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center pt-3 border-top">
                        <div class="col-4">
                            <label class="text-muted small d-block">Fulfilled</label>
                            <span class="fw-bold">{{ number_format($report['fulfillment_orders']) }} Orders</span>
                        </div>
                        <div class="col-4">
                            <label class="text-muted small d-block">Shipped</label>
                            <span class="fw-bold">{{ number_format($report['units_shipped']) }} Units</span>
                        </div>
                        <div class="col-4">
                            <label class="text-muted small d-block">Returned</label>
                            <span class="fw-bold text-danger">{{ number_format($report['returns_qty']) }} Units</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Metrics -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0">Quality Control (Warehouse)</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6 text-center border-end">
                            <h1 class="display-5 fw-bold {{ $report['damage_rate'] > 2 ? 'text-danger' : 'text-success' }} mb-0">{{ number_format($report['damage_rate'], 2) }}%</h1>
                            <span class="text-muted small text-uppercase">Wastage Rate</span>
                        </div>
                        <div class="col-md-6 ps-4">
                            <div class="mb-2">
                                <label class="text-muted small">Wastage Units:</label>
                                <span class="fw-bold text-danger">{{ number_format($report['wastage_entry_qty']) }}</span>
                            </div>
                            <div class="mb-2">
                                <label class="text-muted small">Slow Moving SKUs:</label>
                                <span class="fw-bold text-warning">{{ number_format($report['slow_moving_percent'], 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center pt-3 border-top">
                        <div class="col-6">
                            <label class="text-muted small d-block">Manual Wastage</label>
                            <span class="fw-bold text-danger">{{ number_format($report['wastage_entry_qty']) }} Units</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Supplier Damaged (PO)</label>
                            <span class="fw-bold">{{ number_format($report['damaged_plus_stock']) }} Units</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
