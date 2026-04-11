@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Warehouse Performance</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card no-print">
        <div class="card-body">
            <form action="{{ route('admin.reports.warehouse-performance') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Warehouse</label>
                    <select name="warehouse_id" class="form-select select2_list">
                        <option value="all">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Table -->
    <div class="card border-0 shadow-sm" id="performance-table-card">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center no-print">
            <h5 class="card-title mb-0">Efficiency & Quality Metrics</h5>
            <div class="btn-group">
                <a href="{{ route('admin.reports.warehouse-performance.export', request()->all()) }}" class="btn btn-sm btn-soft-success">
                    <i class="bx bx-download me-1"></i> Export
                </a>
                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport()">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Warehouse</th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Physical stock available before the selected start date">Opening <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Total units received from Purchase Orders during this period">Received <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Units that arrived damaged from the supplier">PO Damaged <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Total units shipped and delivered to customers">Sold <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Total units returned by customers (Saleable + Damaged)">Returns <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Manual stock adjustments (Positive/Negative)">Adjusted <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center" data-bs-toggle="tooltip" title="Final physical stock balance at the end of the period">Total Closing <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center" data-bs-toggle="tooltip" title="Percentage of units shipped vs initial customer demand">Gross Fill % <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Fulfillment success rate after accounting for customer returns">Net Fill % <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center" data-bs-toggle="tooltip" title="Percentage of units lost to internal damage or return defects">Total Wastage % <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center" data-bs-toggle="tooltip" title="How many times inventory was 'cycled' or sold (Cost of Goods Sold / Avg Inventory)">Stock Turnover <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center d-none d-print-table-cell-custom" data-bs-toggle="tooltip" title="Total procurement value of on-hand stock">Value <i class="bx bx-info-circle small"></i></th>
                            <th class="text-center text-muted" data-bs-toggle="tooltip" title="Percentage of unique items with zero sales in this period">Slow % <i class="bx bx-info-circle small"></i></th>
                            <th class="text-end pe-3 actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $row['warehouse_name'] }}</td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['opening_stock']) }}</td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['received_qty']) }}</td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['po_damaged_qty']) }}</td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['sold_qty']) }}</td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['returns_qty']) }}</td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['adjusted_in']) }}</td>
                                <td class="text-center fw-medium">{{ number_format($row['total_closing_stock']) }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress w-50 me-2 d-print-none" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $row['fill_rate'] }}%"></div>
                                        </div>
                                        <span class="small fw-bold">{{ number_format($row['fill_rate'], 1) }}%</span>
                                    </div>
                                </td>
                                <td class="text-center d-none d-print-table-cell-custom">{{ number_format($row['net_fill_rate'], 1) }}%</td>
                                <td class="text-center">
                                    <span class="badge {{ $row['damage_rate'] > 5 ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }}">
                                        {{ number_format($row['damage_rate'], 2) }}%
                                    </span>
                                </td>
                                <td class="text-center fw-bold">{{ number_format($row['stock_turnover'], 2) }}x</td>
                                <td class="text-center d-none d-print-table-cell-custom">${{ number_format($row['inventory_value'], 2) }}</td>
                                <td class="text-center text-muted small">{{ number_format($row['slow_moving_percent'], 1) }}%</td>
                                <td class="text-end pe-3 actions-column">
                                    <a href="{{ route('admin.reports.warehouse-performance.show', [$row['warehouse_id'], 'start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" 
                                       class="btn btn-sm btn-soft-primary">
                                        <i class="bx bx-show me-1"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No data found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
        @if($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-3">
                {{ $reportData->links() }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-print logic for full data view
    $(document).ready(function() {
        if (new URLSearchParams(window.location.search).has('is_print')) {
            // Hide everything first
            $('body > *').hide();
            
            // Create a print container
            const printContainer = $('<div class="print-container"></div>').appendTo('body');
            
            // Add business header
            const bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
            const dateRange = "{{ $filters['start_date'] }} to {{ $filters['end_date'] }}";
            const generatedAt = new Date().toLocaleString();
            
            printContainer.append(`
                <div class="text-center mb-4 border-bottom pb-3">
                    <h1 class="fw-bold mb-1">${bName}</h1>
                    <h3 class="mb-2">Warehouse Performance Summary</h3>
                    <p class="mb-0 text-muted small">Period: ${dateRange} | Generated: ${generatedAt}</p>
                </div>
            `);

            // Clone the table and clean it
            const tableClone = $('#performance-table-card .card-body').clone();
            tableClone.find('.btn-group, .btn, .bx, iconify-icon, .actions-column').remove();
            
            // Show print-only columns
            tableClone.find('.d-none.d-print-table-cell-custom').removeClass('d-none');
            // Hide progress bars
            tableClone.find('.progress').remove();

            printContainer.append(tableClone);

            // Apply print-specific styles
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @media print {
                        @page { size: landscape; margin: 1cm; }
                        body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; }
                        .print-container { padding: 40px !important; }
                        table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; table-layout: fixed; }
                        th, td { border: 1px solid #000 !important; padding: 5px 2px !important; font-size: 9px !important; color: black !important; text-align: center !important; word-wrap: break-word; }
                        th { background-color: #f8f9fa !important; font-weight: bold !important; -webkit-print-color-adjust: exact; }
                        .text-end { text-align: right !important; }
                        .text-center { text-align: center !important; }
                        .fw-bold { font-weight: bold !important; }
                        .text-muted { color: #6c757d !important; }
                        .ps-3 { text-align: left !important; padding-left: 5px !important; }
                        .badge { border: 1px solid #000; padding: 2px 4px; border-radius: 3px; font-size: 8px; color: black !important; background: transparent !important; }
                    }
                `)
                .appendTo('head');

            window.print();
            
            setTimeout(() => {
                if (confirm('Close this print tab?')) window.close();
            }, 500);
        }
    });

    function printFullReport() {
        const url = new URL(window.location.href);
        url.searchParams.set('is_print', '1');
        url.searchParams.delete('page');
        window.open(url.toString(), '_blank');
    }

    function printReportCard(cardId, reportTitle) {
        var content = document.getElementById(cardId);
        if (!content) return;

        var clone = content.cloneNode(true);
        // Hide elements that shouldn't be printed
        var hideElements = clone.querySelectorAll('.btn-group, .btn, .bx, .no-print, .actions-column, .card-header, .card-footer, .d-print-none');
        hideElements.forEach(function(el) {
            el.style.display = 'none';
        });

        var printWin = window.open('', '_blank', 'width=1100,height=800');
        if (!printWin) {
            alert('Please allow popups to print reports.');
            return;
        }

        var bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        var dateRange = "{{ $filters['start_date'] }} to {{ $filters['end_date'] }}";
        var generatedAt = "{{ date('Y-m-d H:i') }}";

        printWin.document.title = 'Report - ' + reportTitle;
        
        var style = printWin.document.createElement('style');
        style.innerHTML = `
            @page { size: landscape; margin: 1cm; }
            body { font-family: sans-serif; padding: 20px; color: #000; }
            .hdr { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
            th, td { border: 1px solid #000 !important; padding: 5px 3px; text-align: center; font-size: 9px; word-wrap: break-word; }
            th { background-color: #f2f2f2 !important; font-weight: bold; -webkit-print-color-adjust: exact; }
            .ps-3 { text-align: left !important; padding-left: 5px !important; }
            .fw-bold { font-weight: bold !important; }
            .text-muted { color: #666 !important; }
            
            /* Reveal hidden Excel columns for print */
            .d-none.d-print-table-cell-custom { display: table-cell !important; }
            
            /* Ensure Actions and other UI are hidden */
            .actions-column, .btn, .bx, .progress, .no-print, .card-header, .card-footer { display: none !important; }
            
            .card { border: none !important; }
        `;
        printWin.document.head.appendChild(style);

        var header = printWin.document.createElement('div');
        header.className = 'hdr';
        header.innerHTML = '<h1>' + bName + '</h1><h3>' + reportTitle + '</h3><p>Period: ' + dateRange + ' | Generated: ' + generatedAt + '</p>';
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
