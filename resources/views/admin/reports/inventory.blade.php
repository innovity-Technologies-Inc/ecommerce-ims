@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Inventory Valuation</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card no-print">
        <div class="card-body">
            <form action="{{ route('admin.reports.inventory') }}" method="GET" class="row g-3">
                <input type="hidden" name="view" value="{{ $view ?? '' }}">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Warehouse</label>
                    <select name="warehouse_id" class="form-select select2_list">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Supplier</label>
                    <select name="supplier_id" class="form-select select2_list">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ ($filters['supplier_id'] ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Batch #</label>
                    <input type="text" name="batch_number" class="form-control" placeholder="Search Batch..." value="{{ $filters['batch_number'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Generate
                    </button>
                </div>

                <div class="col-12 mt-2">
                    <a class="text-primary small fw-bold text-decoration-none" data-bs-toggle="collapse" href="#extraFilters">
                        <i class="bx bx-plus me-1"></i> More Filters
                    </a>
                    <div class="collapse {{ !empty($filters['product_id']) || !empty($filters['category_id']) || !empty($filters['brand_id']) || !empty($filters['include_damaged']) ? 'show' : '' }} mt-3" id="extraFilters">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Include Wastage & Damage?</label>
                                <select name="include_damaged" class="form-select">
                                    <option value="no" {{ ($filters['include_damaged'] ?? 'no') == 'no' ? 'selected' : '' }}>No (Saleable only)</option>
                                    <option value="yes" {{ ($filters['include_damaged'] ?? 'no') == 'yes' ? 'selected' : '' }}>Yes (Include Wastage & Damage)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Category</label>
                                <select name="category_id" class="form-select select2_list">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Brand</label>
                                <select name="brand_id" class="form-select select2_list">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ ($filters['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Product</label>
                                <select name="product_id" class="form-select select2_list">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ ($filters['product_id'] ?? '') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-primary border-4" data-bs-toggle="tooltip" title="Total number of unique products and variants currently in stock">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-primary rounded">
                                <i class="bx bx-package fs-24 text-primary mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Items Types <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($report['totals']['total_items']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4" data-bs-toggle="tooltip" title="Total physical units across all saleable batches and warehouses">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-success rounded">
                                <i class="bx bx-archive fs-24 text-success mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Units In-Stock <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($report['totals']['total_quantity']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-info border-4" data-bs-toggle="tooltip" title="Total procurement value: Sum of (Current Quantity * Unit Cost) for all batches">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-info rounded">
                                <i class="bx bx-dollar-circle fs-24 text-info mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Inventory Valuation <i class="bx bx-info-circle small"></i></h6>
                            <h3 class="mb-0 fw-bold">${{ number_format($report['totals']['total_valuation'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($view))
        <!-- Detailed Paginated View -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.reports.inventory', request()->except('view', 'page')) }}" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bx bx-arrow-back"></i> Dashboard
                    </a>
                    <h5 class="card-title mb-0 d-inline-block">{{ $title }}</h5>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => $view])) }}" class="btn btn-sm btn-soft-success">
                        <i class="bx bx-download me-1"></i> Export
                    </a>
                    <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport()">
                        <i class="bx bx-printer"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0" id="detailed-table-container">
                <div class="table-responsive">
                    @if($view === 'batch')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Batch #</th>
                                    <th>Warehouse</th>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end pe-3">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">{{ $row->name }}</td>
                                        <td>{{ $row->warehouse }}</td>
                                        <td class="small">{{ $row->product }}</td>
                                        <td class="text-center fw-bold">{{ number_format($row->quantity) }}</td>
                                        <td class="text-end text-muted small">${{ number_format($row->unit_cost, 2) }}</td>
                                        <td class="text-end pe-3 fw-bold text-dark">${{ number_format($row->valuation, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">{{ $view === 'warehouse' ? 'Warehouse' : 'Product' }}</th>
                                    <th class="text-center">Units</th>
                                    <th class="text-end pe-3">Valuation (Cost)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $row->name }}</td>
                                        <td class="text-center">{{ number_format($row->quantity) }}</td>
                                        <td class="text-end pe-3 fw-bold">${{ number_format($row->valuation, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
            @if($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-3">
                    {{ $data->links() }}
                </div>
            @endif
            </div>
        </div>
    @else
        <!-- Dashboard Overview Mode -->
        <div class="row g-4">
            <!-- Warehouse Valuation -->
            <div class="col-md-6">
                <div id="card-warehouse" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Warehouse-wise Valuation</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.inventory', array_merge(request()->all(), ['view' => 'warehouse'])) }}" class="btn btn-sm btn-soft-primary text-nowrap">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => 'warehouse'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport('warehouse')">
                                    <i class="bx bx-printer"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Warehouse</th>
                                        <th class="text-center">Units</th>
                                        <th class="text-end pe-3">Valuation (Cost)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['breakdowns']['warehouse'] as $wh)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $wh['name'] }}</td>
                                            <td class="text-center">{{ number_format($wh['quantity']) }}</td>
                                            <td class="text-end pe-3 fw-bold">${{ number_format($wh['valuation'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Valuation -->
            <div class="col-md-6">
                <div id="card-product" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Product-wise Valuation</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.inventory', array_merge(request()->all(), ['view' => 'product'])) }}" class="btn btn-sm btn-soft-primary text-nowrap">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => 'product'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport('product')">
                                    <i class="bx bx-printer"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Product</th>
                                        <th class="text-center">Units</th>
                                        <th class="text-end pe-3">Valuation (Cost)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['breakdowns']['product'] as $prod)
                                        <tr>
                                            <td class="ps-3 small fw-medium">{{ $prod['name'] }}</td>
                                            <td class="text-center">{{ number_format($prod['quantity']) }}</td>
                                            <td class="text-end pe-3 fw-bold">${{ number_format($prod['valuation'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Batch-wise Detail -->
            <div class="col-12 mt-4">
                <div id="card-batch" class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Batch-wise Inventory Breakdown</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.inventory', array_merge(request()->all(), ['view' => 'batch'])) }}" class="btn btn-sm btn-soft-primary text-nowrap">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => 'batch'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport('batch')">
                                    <i class="bx bx-printer"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Batch #</th>
                                        <th>Warehouse</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end pe-3">Total Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report['breakdowns']['batch'] as $batch)
                                        <tr>
                                            <td class="ps-3 fw-bold text-primary">{{ $batch['name'] }}</td>
                                            <td>{{ $batch['warehouse'] }}</td>
                                            <td class="small">{{ $batch['product'] }}</td>
                                            <td class="text-center fw-bold">{{ number_format($batch['quantity']) }}</td>
                                            <td class="text-end text-muted small">${{ number_format($batch['unit_cost'], 2) }}</td>
                                            <td class="text-end pe-3 fw-bold text-dark">${{ number_format($batch['valuation'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No inventory records found matching your filters.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
            const dateStr = new Date().toLocaleString();
            const reportTitle = "{{ $title ?? 'Inventory Report' }}";
            const asOfDate = "{{ $filters['end_date'] ?? 'Current' }}";
            
            printContainer.append(`
                <div class="text-center mb-4 border-bottom pb-3">
                    <h1 class="fw-bold mb-1">${bName}</h1>
                    <h3 class="mb-2">${reportTitle}</h3>
                    <p class="mb-0 text-muted small">Snapshot Date: ${asOfDate} | Generated: ${dateStr}</p>
                </div>
            `);

            // Clone the table and clean it
            const tableClone = $('#detailed-table-container').clone();
            tableClone.find('.btn-group, .btn, .bx, iconify-icon').remove();
            printContainer.append(tableClone);

            // Apply print-specific styles
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @media print {
                        body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; }
                        .print-container { padding: 40px !important; }
                        table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; }
                        th, td { border: 1px solid #000 !important; padding: 10px !important; font-size: 12px !important; color: black !important; }
                        th { background-color: #f8f9fa !important; font-weight: bold !important; -webkit-print-color-adjust: exact; }
                        .text-end { text-align: right !important; }
                        .text-center { text-align: center !important; }
                        .fw-bold { font-weight: bold !important; }
                        .text-muted { color: #6c757d !important; }
                    }
                `)
                .appendTo('head');

            window.print();
            
            setTimeout(() => {
                if (confirm('Close this print tab?')) window.close();
            }, 500);
        }
    });

    function printFullReport(view = null) {
        const url = new URL(window.location.href);
        url.searchParams.set('is_print', '1');
        if (view) {
            url.searchParams.set('view', view);
        }
        url.searchParams.delete('page');
        window.open(url.toString(), '_blank');
    }

    function printReportCard(cardId, reportTitle) {
        var content = document.getElementById(cardId);
        if (!content) return;

        var clone = content.cloneNode(true);
        var btnGroups = clone.querySelectorAll('.btn-group, .btn, .bx, iconify-icon');
        for (var i = 0; i < btnGroups.length; i++) {
            btnGroups[i].style.display = 'none';
        }

        var printWin = window.open('', '_blank', 'width=1000,height=800');
        if (!printWin) {
            alert('Please allow popups to print reports.');
            return;
        }

        var bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        var dateStr = new Date().toLocaleString();
        var asOfDate = "{{ $filters['end_date'] ?? 'Current' }}";

        printWin.document.title = 'Inventory Report - ' + reportTitle;
        
        var style = printWin.document.createElement('style');
        style.innerHTML = 'body{font-family:sans-serif;padding:40px;color:#000} .hdr{text-align:center;margin-bottom:30px;border-bottom:2px solid #000} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{border:1px solid #000 !important;padding:10px;text-align:left;font-size:13px;color:#000 !important} th{background-color:#f2f2f2 !important;font-weight:bold; -webkit-print-color-adjust: exact;} .text-end{text-align:right !important} .text-center{text-align:center !important} .fw-bold{font-weight:bold !important}';
        printWin.document.head.appendChild(style);

        var header = printWin.document.createElement('div');
        header.className = 'hdr';
        header.innerHTML = '<h1>' + bName + '</h1><h3>Inventory Report: ' + reportTitle + '</h3><p>Snapshot Date: ' + asOfDate + ' | Generated: ' + dateStr + '</p>';
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

    $(document).ready(function() {
        console.log("Inventory Report Dashboard Initialized");
    });
</script>
@endsection
