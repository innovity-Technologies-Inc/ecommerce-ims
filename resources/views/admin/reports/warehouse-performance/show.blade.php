@extends('admin.structure.app')

@section('content')
<div class="container-xxl" id="performance-detail-report">
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <a href="{{ route('admin.reports.warehouse-performance', ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" class="btn btn-secondary btn-sm mb-2 hover-lift">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
            <h4 class="mb-0 fw-bold text-dark">Warehouse: <span class="text-primary">{{ $warehouse->name }}</span></h4>
            <p class="text-muted small mb-0"><i class="bx bx-calendar me-1"></i>Performance Period: <strong>{{ $filters['start_date'] }}</strong> to <strong>{{ $filters['end_date'] }}</strong></p>
        </div>
        <div class="no-print d-flex gap-2">
            <button type="button" class="btn btn-sm btn-soft-secondary hover-lift" onclick="printDetailedReport()">
                <i class="bx bx-printer me-1"></i> Print Full Report
            </button>
        </div>
    </div>

    <div id="printable-area">
        <!-- Summary Statistics Top Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-primary border-4 aesthetic-card" data-bs-toggle="tooltip" title="Physical stock available in this warehouse before the selected start date">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Opening Stock <i class="bx bx-info-circle small"></i></h6>
                                <h3 class="mb-0 fw-bold text-dark">{{ number_format($report['opening_stock'] ?? 0) }}</h3>
                            </div>
                            <div class="avatar-sm bg-soft-primary rounded">
                                <i class="bx bx-door-open fs-3 text-primary mt-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-success border-4 aesthetic-card" data-bs-toggle="tooltip" title="Total additions to stock (Received + Returns + Adjustments + PO Damaged) during this period">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Inflows (+) <i class="bx bx-info-circle small"></i></h6>
                                <h3 class="mb-0 fw-bold text-success">{{ number_format(($report['received_qty'] ?? 0) + ($report['returns_qty'] ?? 0) + ($report['adjusted_in'] ?? 0) + ($report['po_damaged_qty'] ?? 0)) }}</h3>
                            </div>
                            <div class="avatar-sm bg-soft-success rounded">
                                <i class="bx bx-trending-up fs-3 text-success mt-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-danger border-4 aesthetic-card" data-bs-toggle="tooltip" title="Total physical stock removals (Sold + RTV + Internal Wastage) during this period">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Outflows (-) <i class="bx bx-info-circle small"></i></h6>
                                <h3 class="mb-0 fw-bold text-danger">{{ number_format(($report['sold_qty'] ?? 0) + ($report['rtv_qty'] ?? 0) + ($report['total_wastage_qty'] ?? 0)) }}</h3>
                            </div>
                            <div class="avatar-sm bg-soft-danger rounded">
                                <i class="bx bx-trending-down fs-3 text-danger mt-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-info border-4 aesthetic-card" data-bs-toggle="tooltip" title="Actual live physical balance on-hand at the end of the period (matches ledger reconciliation)">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Closing Snapshot <i class="bx bx-info-circle small"></i></h6>
                                <h3 class="mb-0 fw-bold text-info">{{ number_format($report['total_closing_stock'] ?? 0) }}</h3>
                            </div>
                            <div class="avatar-sm bg-soft-info rounded">
                                <i class="bx bx-package fs-3 text-info mt-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Stock Details Table -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm aesthetic-card h-100 overflow-hidden">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-2 text-primary"></i>Stock Movement Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0 text-uppercase small fw-bold">Category</th>
                                        <th class="text-center py-3 border-0 text-uppercase small fw-bold">Units</th>
                                        <th class="py-3 border-0 text-uppercase small fw-bold">Description / Breakdown</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 fw-bold text-dark">Opening Balance</td>
                                        <td class="text-center fw-bold">{{ number_format($report['opening_stock'] ?? 0) }}</td>
                                        <td class="text-muted small">Physical stock before {{ $filters['start_date'] }}</td>
                                    </tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-success fw-semibold">Units Received</td>
                                        <td class="text-center text-success fw-bold">+{{ number_format($report['received_qty'] ?? 0) }}</td>
                                        <td class="text-muted small">New stock from verified Purchase Orders</td>
                                    </tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-success fw-semibold">Customer Returns</td>
                                        <td class="text-center text-success fw-bold">+{{ number_format($report['returns_qty'] ?? 0) }}</td>
                                        <td class="text-muted small">Items returned by customers (Saleable + Damaged)</td>
                                    </tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-success fw-semibold">Supplier Damaged</td>
                                        <td class="text-center text-success fw-bold">+{{ number_format($report['po_damaged_qty'] ?? 0) }}</td>
                                        <td class="text-muted small">Units received damaged (Damaged Pool)</td>
                                    </tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-success fw-semibold">Adjusted In</td>
                                        <td class="text-center text-success fw-bold">+{{ number_format($report['adjusted_in'] ?? 0) }}</td>
                                        <td class="text-muted small">Manual positive corrections</td>
                                    </tr>
                                    <tr class="bg-light-soft"><td colspan="3" style="height: 8px;"></td></tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-danger fw-semibold">Units Sold</td>
                                        <td class="text-center text-danger fw-bold">-{{ number_format($report['sold_qty'] ?? 0) }}</td>
                                        <td class="text-muted small">Orders shipped and delivered</td>
                                    </tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-danger fw-semibold">RTV Dispatch</td>
                                        <td class="text-center text-danger fw-bold">-{{ number_format($report['rtv_qty'] ?? 0) }}</td>
                                        <td class="text-muted small">Returns sent back to suppliers</td>
                                    </tr>
                                    <tr class="table-row-hover">
                                        <td class="ps-4 text-danger fw-semibold">Internal Wastage</td>
                                        <td class="text-center text-danger fw-bold">-{{ number_format($report['total_wastage_qty'] ?? 0) }}</td>
                                        <td class="text-muted small">Warehouse damage and return damage</td>
                                    </tr>
                                    <tr class="bg-primary text-white fw-bold">
                                        <td class="ps-4 py-3 text-white">Closing Physical Balance</td>
                                        <td class="text-center py-3 text-white">{{ number_format($report['total_closing_stock'] ?? 0) }}</td>
                                        <td class="py-3 text-white">Live Units on-hand in warehouse</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Side Cards -->
            <div class="col-md-4">
                <!-- Valuation Card -->
                <div class="card border-0 shadow-sm mb-4 border-top border-primary border-4 aesthetic-card" data-bs-toggle="tooltip" title="Total procurement value of all saleable units currently in this warehouse">
                    <div class="card-body p-4 text-center">
                        <div class="avatar-lg bg-soft-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bx bx-dollar-circle fs-1 text-primary"></i>
                        </div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-2">Live Inventory Value <i class="bx bx-info-circle small"></i></h6>
                        <h2 class="fw-bold text-dark mb-3">{{ $gs->currency ?? '$' }}{{ number_format($report['inventory_value'] ?? 0, 2) }}</h2>
                        <div class="bg-light p-2 px-3 rounded-pill d-inline-block" data-bs-toggle="tooltip" title="How many times the average inventory was sold during this period (Cost of Goods Sold / Inventory Value)">
                            <span class="small text-muted me-2">Stock Turnover:</span>
                            <span class="fw-bold text-primary">{{ number_format($report['stock_turnover'] ?? 0, 2) }}x <i class="bx bx-info-circle x-small"></i></span>
                        </div>
                    </div>
                </div>

                <!-- Efficiency Card -->
                <div class="card border-0 shadow-sm mb-4 aesthetic-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="bx bx-rocket me-2 text-success"></i>Fulfillment Efficiency</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4" data-bs-toggle="tooltip" title="Percentage of units shipped versus initial customer demand">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-secondary fw-semibold">Gross Fill Rate <i class="bx bx-info-circle x-small"></i></span>
                                <span class="fw-bold text-success">{{ number_format($report['fill_rate'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="progress shadow-sm" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-success progress-bar-striped" style="width: {{ $report['fill_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-4" data-bs-toggle="tooltip" title="Actual fulfillment success rate after subtracting returned units">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-secondary fw-semibold">Net Fulfillment <i class="bx bx-info-circle x-small"></i></span>
                                <span class="fw-bold text-primary">{{ number_format($report['net_fill_rate'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="progress shadow-sm" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-primary progress-bar-striped" style="width: {{ $report['net_fill_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center bg-soft-danger p-2 px-3 rounded-3 mt-2" data-bs-toggle="tooltip" title="Percentage of units returned by customers relative to units shipped">
                            <span class="small fw-semibold text-danger">Return Rate <i class="bx bx-info-circle x-small"></i></span>
                            <span class="h6 fw-bold text-danger mb-0">{{ number_format($report['return_rate'] ?? 0, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Health Card -->
                <div class="card border-0 shadow-sm border-bottom border-warning border-4 aesthetic-card">
                    <div class="card-body p-4">
                        <h6 class="card-title mb-4 fw-bold small text-uppercase text-muted"><i class="bx bx-heart me-2 text-warning"></i>Inventory Health</h6>
                        <div class="row text-center">
                            <div class="col-6 border-end" data-bs-toggle="tooltip" title="Number of items at or below their assigned minimum stock levels">
                                <div class="p-2">
                                    <h3 class="mb-0 fw-bold {{ ($report['low_stock_count'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">{{ $report['low_stock_count'] ?? 0 }}</h3>
                                    <span class="text-muted x-small fw-bold">LOW STOCK SKUs <i class="bx bx-info-circle x-small"></i></span>
                                </div>
                            </div>
                            <div class="col-6" data-bs-toggle="tooltip" title="Percentage of unique items that had zero sales activity during this period">
                                <div class="p-2">
                                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($report['slow_moving_percent'] ?? 0, 1) }}%</h3>
                                    <span class="text-muted x-small fw-bold">SLOW MOVING % <i class="bx bx-info-circle x-small"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Aesthetic Animations & Effects */
    .aesthetic-card {
        transition: all 0.3s ease-in-out;
        border-radius: 12px !important;
    }
    .aesthetic-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12) !important;
    }
    .hover-lift {
        transition: transform 0.2s ease;
    }
    .hover-lift:hover {
        transform: scale(1.05);
    }
    .avatar-sm {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .x-small { font-size: 10px; }
    .bg-light-soft { background-color: #fcfdfe; }
    .table-row-hover:hover { background-color: rgba(13, 110, 253, 0.03); }
    .custom-table th { font-size: 11px; letter-spacing: 0.5px; }
    
    /* Soft Backgrounds */
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }

    @media print {
        @page { size: landscape; margin: 1cm; }
        .no-print { display: none !important; }
        body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; font-family: sans-serif; }
        .container-xxl { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; margin-bottom: 15px !important; break-inside: avoid; border-radius: 0 !important; }
        .card-header { background-color: #f8f9fa !important; border-bottom: 1px solid #ddd !important; -webkit-print-color-adjust: exact; }
        .table-light th { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
        .row { display: flex !important; flex-wrap: wrap !important; }
        .col-md-8 { width: 65% !important; }
        .col-md-4 { width: 35% !important; }
        .col-md-3 { width: 25% !important; }
        .text-success { color: #198754 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-primary { color: #0d6efd !important; }
        .text-info { color: #0dcaf0 !important; }
        .text-warning { color: #ffc107 !important; }
        .bg-success { background-color: #198754 !important; }
        .bg-primary { background-color: #0d6efd !important; }
        .bg-info { background-color: #0dcaf0 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .badge { border: 1px solid #000; color: #000 !important; background: transparent !important; }
    }
</style>
@endpush

@section('scripts')
<script>
    function printDetailedReport() {
        var content = document.getElementById('printable-area');
        if (!content) return;

        var clone = content.cloneNode(true);
        // Remove interactive elements from clone
        var extras = clone.querySelectorAll('.btn-group, .btn, .bx, iconify-icon');
        for (var i = 0; i < extras.length; i++) {
            extras[i].remove();
        }

        var printWin = window.open('', '_blank', 'width=1200,height=800');
        if (!printWin) {
            alert('Please allow popups to print reports.');
            return;
        }

        var bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        var dateStr = new Date().toLocaleString();
        var reportTitle = "Warehouse Performance Detail: {{ $warehouse->name }}";
        var periodStr = "Period: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}";

        printWin.document.title = reportTitle;
        
        var style = printWin.document.createElement('style');
        style.innerHTML = `
            body { font-family: sans-serif; padding: 30px; color: #000; background: #fff; }
            .hdr { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .hdr h1 { margin: 0; font-size: 24px; }
            .hdr h3 { margin: 5px 0; font-size: 18px; }
            .hdr p { margin: 0; font-size: 12px; color: #666; }
            .row { display: flex; flex-wrap: wrap; margin-right: -10px; margin-left: -10px; }
            .col-md-3 { width: 25%; padding: 0 10px; box-sizing: border-box; }
            .col-md-8 { width: 65%; padding: 0 10px; box-sizing: border-box; }
            .col-md-4 { width: 35%; padding: 0 10px; box-sizing: border-box; }
            .card { border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; overflow: hidden; background: #fff; }
            .card-body { padding: 15px; }
            .card-header { background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; font-weight: bold; }
            .border-start { border-left-width: 4px !important; }
            .border-primary { border-left-color: #0d6efd !important; }
            .border-success { border-left-color: #198754 !important; }
            .border-danger { border-left-color: #dc3545 !important; }
            .border-info { border-left-color: #0dcaf0 !important; }
            .text-success { color: #198754 !important; }
            .text-danger { color: #dc3545 !important; }
            .text-muted { color: #6c757d !important; }
            .small { font-size: 12px; }
            .text-uppercase { text-transform: uppercase; }
            .fw-bold { font-weight: bold; }
            h3, h4, h2 { margin-top: 0; margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #eee; padding: 8px; text-align: left; font-size: 12px; }
            .table-light th { background: #f8f9fa; }
            .text-center { text-align: center; }
            .bg-light { background: #f8f9fa; }
            .bg-primary { background: #0d6efd; color: #fff !important; }
            .progress { height: 10px; background: #eee; border-radius: 5px; overflow: hidden; margin: 5px 0; }
            .progress-bar { height: 100%; }
            .bg-success { background: #198754; }
            .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; background: #eee; }
            @media print {
                body { padding: 0; }
                .card { break-inside: avoid; }
            }
        `;
        printWin.document.head.appendChild(style);

        var header = printWin.document.createElement('div');
        header.className = 'hdr';
        header.innerHTML = '<h1>' + bName + '</h1><h3>' + reportTitle + '</h3><p>' + periodStr + ' | Generated: ' + dateStr + '</p>';
        printWin.document.body.appendChild(header);

        var bodyContent = printWin.document.createElement('div');
        bodyContent.innerHTML = clone.innerHTML;
        printWin.document.body.appendChild(bodyContent);

        setTimeout(function() {
            printWin.focus();
            printWin.print();
            printWin.close();
        }, 500);
    }
</script>
@endsection
