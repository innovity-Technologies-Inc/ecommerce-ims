@extends('admin.structure.app')

@section('content')
<div class="container-xxl" id="performance-detail-report">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <a href="{{ route('admin.reports.warehouse-performance', ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" class="btn btn-sm btn-outline-secondary mb-2 no-print">
                <i class="bx bx-arrow-back"></i> Back to Dashboard
            </a>
            <h4 class="mb-0">Warehouse: {{ $warehouse->name }}</h4>
            <p class="text-muted small mb-0">Performance Period: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</p>
        </div>
        <div class="no-print">
            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printDetailedReport()">
                <i class="bx bx-printer"></i> Print Full Report
            </button>
        </div>
    </div>

    <!-- Print Header (Hidden on screen) -->
    <div class="d-none d-print-block">
        <div class="text-center mb-4 border-bottom pb-3">
            <h1 class="fw-bold mb-1">{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}</h1>
            <h3 class="mb-2">Warehouse Performance Detail: {{ $warehouse->name }}</h3>
            <p class="mb-0 text-muted small">Period: {{ $filters['start_date'] }} to {{ $filters['end_date'] }} | Generated: {{ date('Y-m-d H:i') }}</p>
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
                            <h3 class="fw-bold text-success mb-0">+{{ number_format($report['received_qty'] + $report['returns_qty'] + $report['adjusted_in'] + $report['po_damaged_qty']) }}</h3>
                            <div class="small text-muted mt-1">
                                Rcv: {{ $report['received_qty'] }} | Dmg: {{ $report['po_damaged_qty'] }} | Ret: {{ $report['returns_qty'] }} | Adj: {{ $report['adjusted_in'] }}
                            </div>
                        </div>
                        <div class="col-md-1 text-center d-none d-md-flex align-items-center justify-content-center">
                            <i class="bx bx-minus text-muted fs-4"></i>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small text-uppercase fw-semibold">Total Outflows</label>
                            <h3 class="fw-bold text-danger mb-0">-{{ number_format($report['sold_qty'] + $report['rtv_qty'] + $report['total_wastage_qty']) }}</h3>
                            <div class="small text-muted mt-1">
                                Sold: {{ $report['sold_qty'] }} | RTV: {{ $report['rtv_qty'] }} | Wst: {{ $report['total_wastage_qty'] }}
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
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary"><i class="bx bxs-circle text-danger me-1 small"></i> Damaged:</span>
                                <span class="fw-bold h5 mb-0">{{ number_format($report['po_damaged_closing']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary"><i class="bx bxs-circle text-warning me-1 small"></i> Wastage:</span>
                                <span class="fw-bold h5 mb-0">{{ number_format($report['wastage_closing']) }}</span>
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                <span class="small text-muted italic">Total Inflow Damaged: {{ $report['po_damaged_qty'] }} units</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                        <div class="card-body p-3 d-flex flex-column justify-content-center">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Live Warehouse Snapshot</label>
                            <div class="d-flex align-items-center justify-content-between">
                                <h2 class="fw-bold mb-0 text-dark">{{ number_format($report['total_closing_stock']) }}</h2>
                                <div class="bg-soft-primary p-2 rounded">
                                    <i class="bx bx-package fs-3 text-primary"></i>
                                </div>
                            </div>
                            <span class="small text-muted">Total Physical Units On-Hand</span>
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
                                <label class="text-muted small">Total Wastage:</label>
                                <span class="fw-bold text-danger">{{ number_format($report['total_wastage_qty']) }} Units</span>
                            </div>
                            <div class="mb-2">
                                <label class="text-muted small">Slow Moving SKUs:</label>
                                <span class="fw-bold text-warning">{{ number_format($report['slow_moving_percent'], 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center pt-3 border-top">
                        <div class="col-6">
                            <label class="text-muted small d-block">Wastage (Internal+Returns)</label>
                            <span class="fw-bold text-danger">{{ number_format($report['total_wastage_qty']) }} Units</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Supplier Damaged (PO)</label>
                            <span class="fw-bold">{{ number_format($report['po_damaged_qty']) }} Units</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    function printDetailedReport() {
        window.print();
    }
</script>

<style>
    @media print {
        @page { size: portrait; margin: 1.5cm; }
        .no-print { display: none !important; }
        body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; font-family: sans-serif; }
        .container-xxl { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
        
        .card { 
            border: 1px solid #000 !important; 
            box-shadow: none !important; 
            margin-bottom: 20px !important; 
            break-inside: avoid;
        }
        
        .card-header { 
            background-color: #f8f9fa !important; 
            border-bottom: 1px solid #000 !important; 
            -webkit-print-color-adjust: exact;
        }
        
        .text-success { color: #198754 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-primary { color: #0d6efd !important; }
        .text-info { color: #0dcaf0 !important; }
        .text-warning { color: #ffc107 !important; }
        .badge { border: 1px solid #000; color: #000 !important; background: transparent !important; }
        
        .progress { border: 1px solid #000 !important; background: #fff !important; }
        .progress-bar { background-color: #000 !important; }
        
        /* Ensure layout columns work in print */
        .row { display: flex !important; flex-wrap: wrap !important; }
        .col-md-8 { width: 66.66% !important; }
        .col-md-4 { width: 33.33% !important; }
        .col-md-6 { width: 50% !important; }
        .col-md-3 { width: 25% !important; }
    }
</style>
@endsection
