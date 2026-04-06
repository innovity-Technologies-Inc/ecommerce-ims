@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Inventory Valuation</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('admin.reports.inventory') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">As-of Date</label>
                    <input type="date" name="as_of_date" class="form-control" value="{{ $filters['as_of_date'] ?? '' }}">
                    <div class="form-text xsmall">Leave blank for current stock</div>
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
                    <label class="form-label small fw-bold">Include Damaged?</label>
                    <select name="include_damaged" class="form-select">
                        <option value="no" {{ ($filters['include_damaged'] ?? 'no') == 'no' ? 'selected' : '' }}>No (Saleable only)</option>
                        <option value="yes" {{ ($filters['include_damaged'] ?? 'no') == 'yes' ? 'selected' : '' }}>Yes (Include Wastage)</option>
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
                        <i class="bx bx-plus me-1"></i> Catalog Filters
                    </a>
                    <div class="collapse {{ !empty($filters['product_id']) || !empty($filters['category_id']) || !empty($filters['brand_id']) ? 'show' : '' }} mt-3" id="extraFilters">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Category</label>
                                <select name="category_id" class="form-select select2_list">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Brand</label>
                                <select name="brand_id" class="form-select select2_list">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ ($filters['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
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
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-primary rounded">
                                <i class="bx bx-package fs-24 text-primary mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Items Types</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($report['totals']['total_items']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-success rounded">
                                <i class="bx bx-archive fs-24 text-success mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Units In-Stock</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($report['totals']['total_quantity']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-info rounded">
                                <i class="bx bx-dollar-circle fs-24 text-info mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Total Inventory Valuation</h6>
                            <h3 class="mb-0 fw-bold">${{ number_format($report['totals']['total_valuation'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Warehouse Valuation -->
        <div class="col-md-6">
            <div id="card-warehouse" class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Warehouse-wise Valuation</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => 'warehouse'])) }}" class="btn btn-sm btn-soft-success">
                            <i class="bx bx-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-warehouse', 'Warehouse Valuation')">
                            <i class="bx bx-printer"></i>
                        </button>
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
                    <h5 class="card-title mb-0">Product-wise Valuation (Top 50)</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => 'product'])) }}" class="btn btn-sm btn-soft-success">
                            <i class="bx bx-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-product', 'Product Valuation')">
                            <i class="bx bx-printer"></i>
                        </button>
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
                    <h5 class="card-title mb-0">Batch-wise Inventory Breakdown (Recent 50)</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.reports.inventory.export', array_merge(request()->all(), ['type' => 'batch'])) }}" class="btn btn-sm btn-soft-success">
                            <i class="bx bx-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-batch', 'Batch Breakdown')">
                            <i class="bx bx-printer"></i>
                        </button>
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
</div>
@endsection

@section('scripts')
<script>
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
        var asOfDate = "{{ $filters['as_of_date'] ?? 'Current' }}";

        printWin.document.title = 'Inventory Report - ' + reportTitle;
        
        var style = printWin.document.createElement('style');
        style.innerHTML = 'body{font-family:sans-serif;padding:40px;color:#000} .hdr{text-align:center;margin-bottom:30px;border-bottom:2px solid #000} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{border:1px solid #000 !important;padding:10px;text-align:left;font-size:13px;color:#000 !important} th{background-color:#f2f2f2 !important;font-weight:bold; -webkit-print-color-adjust: exact;} .text-end{text-align:right !important} .text-center{text-align:center !important} .fw-bold{font-weight:bold !important}';
        printWin.document.head.appendChild(style);

        var header = printWin.document.createElement('div');
        header.className = 'hdr';
        header.innerHTML = '<h1>' + bName + '</h1><h3>Inventory Report: ' + reportTitle + '</h3><p>As-of Date: ' + asOfDate + ' | Generated: ' + dateStr + '</p>';
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
