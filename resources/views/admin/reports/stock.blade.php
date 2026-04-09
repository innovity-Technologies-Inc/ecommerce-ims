@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Stock Reports</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('admin.reports.stock') }}" method="GET" class="row g-3">
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
                    <input type="text" name="batch_number" class="form-control" placeholder="Search..." value="{{ $filters['batch_number'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Apply
                    </button>
                </div>

                <div class="col-12 mt-2">
                    <a class="text-primary small fw-bold text-decoration-none" data-bs-toggle="collapse" href="#extraFilters">
                        <i class="bx bx-plus me-1"></i> More Filters
                    </a>
                    <div class="collapse {{ !empty($filters['product_id']) || !empty($filters['category_id']) || !empty($filters['brand_id']) || !empty($filters['low_stock_only']) ? 'show' : '' }} mt-3" id="extraFilters">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Low Stock Only?</label>
                                <select name="low_stock_only" class="form-select">
                                    <option value="no" {{ ($filters['low_stock_only'] ?? 'no') == 'no' ? 'selected' : '' }}>No</option>
                                    <option value="yes" {{ ($filters['low_stock_only'] ?? 'no') == 'yes' ? 'selected' : '' }}>Yes</option>
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
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-primary rounded">
                                <i class="bx bx-package fs-24 text-primary mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">In-Stock Units</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($summary['total_qty']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-danger rounded">
                                <i class="bx bx-error fs-24 text-danger mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Damaged Units</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($summary['damaged_qty']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-warning rounded">
                                <i class="bx bx-trending-down fs-24 text-warning mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Low Stock Alerts</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($summary['low_stock_count']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-success rounded">
                                <i class="bx bx-dollar-circle fs-24 text-success mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Inventory Value</h6>
                            <h3 class="mb-0 fw-bold text-success">${{ number_format($summary['total_value'], 2) }}</h3>
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
                    <a href="{{ route('admin.reports.stock', request()->except('view', 'page')) }}" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bx bx-arrow-back"></i> Dashboard
                    </a>
                    <h5 class="card-title mb-0 d-inline-block">{{ $title }}</h5>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.reports.stock.export', request()->all()) }}" class="btn btn-sm btn-soft-success">
                        <i class="bx bx-download me-1"></i> Export
                    </a>
                    <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport()">
                        <i class="bx bx-printer"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0" id="detailed-table-container">
                <div class="table-responsive">
                    @if($view === 'warehouse' || $view === 'product')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">{{ $view === 'warehouse' ? 'Warehouse' : 'Product' }}</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Damaged</th>
                                    <th class="text-end pe-3">Valuation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-bold">{{ $row->name }}</td>
                                        <td class="text-center">{{ number_format($row->quantity) }}</td>
                                        <td class="text-center">{{ number_format($row->damaged_quantity) }}</td>
                                        <td class="text-end pe-3 fw-bold">${{ number_format($row->valuation, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($view === 'batch')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Batch / SKU</th>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-3">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold">{{ $row->batch_number }}</div>
                                            <small class="text-muted">SKU: {{ $row->sku }}</small>
                                        </td>
                                        <td>{{ $row->product_name }}</td>
                                        <td>{{ $row->warehouse_name }}</td>
                                        <td class="text-center fw-bold">{{ number_format($row->current_quantity) }}</td>
                                        <td class="text-center">
                                            @if($row->is_low_stock)
                                                <span class="badge bg-soft-danger text-danger">Low Stock</span>
                                            @else
                                                <span class="badge bg-soft-success text-success">Healthy</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 fw-bold">${{ number_format($row->inventory_value, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($view === 'movement')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Product</th>
                                    <th>Warehouse / Batch</th>
                                    <th class="text-center">Change</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 small">{{ \Carbon\Carbon::parse($row->created_at)->format('d M, Y h:i A') }}</td>
                                        <td>{{ $row->product_name }}</td>
                                        <td>{{ $row->warehouse_name }} <br><small class="text-muted">Batch: {{ $row->batch_number }}</small></td>
                                        <td class="text-center fw-bold {{ $row->change_qty > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $row->change_qty > 0 ? '+' : '' }}{{ $row->change_qty }}
                                        </td>
                                        <td><span class="badge bg-soft-info text-info">{{ str_replace('_', ' ', $row->transaction_type) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($view === 'aging')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Batch #</th>
                                    <th>Warehouse</th>
                                    <th>Supplier</th>
                                    <th class="text-center">Days Old</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-bold">{{ $row->batch_number }}</td>
                                        <td>{{ $row->warehouse_name }}</td>
                                        <td>{{ $row->supplier_name }}</td>
                                        <td class="text-center fw-bold">{{ $row->age_days }} Days</td>
                                        <td class="text-center">
                                            @if($row->age_days > 90)
                                                <span class="badge bg-soft-danger text-danger">Stagnant</span>
                                            @elseif($row->age_days > 30)
                                                <span class="badge bg-soft-warning text-warning">Aging</span>
                                            @else
                                                <span class="badge bg-soft-success text-success">Fresh</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($view === 'wastage_product' || $view === 'wastage_warehouse' || $view === 'wastage_batch')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th class="text-center">Wastage Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-bold">{{ $row->name }}</td>
                                        <td class="text-center text-danger fw-bold">{{ number_format($row->quantity) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($view === 'serial')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Serial #</th>
                                    <th>Product</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-3">Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">{{ $row->serial_no }}</td>
                                        <td>{{ $row->product_name }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $row->stock_status === 'in_stock' ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                                {{ ucfirst($row->stock_status) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3 small text-muted">{{ \Carbon\Carbon::parse($row->updated_at)->format('d M, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $data->links() }}
            </div>
        </div>
    @else
        <!-- Dashboard Overview Mode -->
        <div class="row g-4 mb-4">
            <!-- Warehouse Breakdown -->
            <div class="col-md-6">
                <div id="card-warehouse" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark fw-bold">Stock by Warehouse</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['view' => 'warehouse'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-warehouse', 'Stock by Warehouse')"><i class="bx bx-printer"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Warehouse</th><th class="text-center">Qty</th><th class="text-end pe-3">Value</th></tr></thead>
                            <tbody>
                                @foreach($breakdowns['warehouse'] as $row)
                                    <tr><td class="ps-3">{{ $row->name }}</td><td class="text-center">{{ $row->quantity }}</td><td class="text-end pe-3 fw-bold">${{ number_format($row->valuation, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Product Breakdown -->
            <div class="col-md-6">
                <div id="card-product" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark fw-bold">Stock by Product</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['view' => 'product'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-product', 'Stock by Product')"><i class="bx bx-printer"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Product</th><th class="text-center">Qty</th><th class="text-end pe-3">Value</th></tr></thead>
                            <tbody>
                                @foreach($breakdowns['product'] as $row)
                                    <tr><td class="ps-3 small">{{ $row->name }}</td><td class="text-center">{{ $row->quantity }}</td><td class="text-end pe-3 fw-bold">${{ number_format($row->valuation, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Batch Aging Breakdown -->
            <div class="col-md-6">
                <div id="card-aging" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark fw-bold">Batch Aging (Stagnant Stock)</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['view' => 'aging'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-aging', 'Batch Aging')"><i class="bx bx-printer"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Batch</th><th class="text-center">Age</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($breakdowns['aging'] as $row)
                                    <tr><td class="ps-3">{{ $row->batch_number }}</td><td class="text-center">{{ $row->age_days }} Days</td><td><span class="badge {{ $row->age_days > 30 ? 'bg-soft-warning text-warning' : 'bg-soft-success text-success' }}">{{ $row->age_days > 30 ? 'Aging' : 'Fresh' }}</span></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Wastage Breakdowns -->
            <div class="col-md-6">
                <div id="card-wastage-product" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark fw-bold">Wastage by Product</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['view' => 'wastage_product'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-wastage-product', 'Product Wastage')"><i class="bx bx-printer"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Product</th><th class="text-center">Wastage Qty</th></tr></thead>
                            <tbody>
                                @foreach($breakdowns['wastage_product'] as $row)
                                    <tr><td class="ps-3">{{ $row->name }}</td><td class="text-center fw-bold text-danger">{{ $row->quantity }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Stock Movements (Full Width) -->
            <div class="col-12 mt-4">
                <div id="card-movement" class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark fw-bold">Recent Stock Movements</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['view' => 'movement'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-movement', 'Recent Movements')"><i class="bx bx-printer"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Date</th><th>Product</th><th class="text-center">Change</th><th>Type</th></tr></thead>
                            <tbody>
                                @foreach($breakdowns['movement'] as $row)
                                    <tr><td class="ps-3 small">{{ \Carbon\Carbon::parse($row->created_at)->format('d M, Y') }}</td><td>{{ $row->product_name }}</td><td class="text-center fw-bold {{ $row->change_qty > 0 ? 'text-success' : 'text-danger' }}">{{ $row->change_qty > 0 ? '+' : '' }}{{ $row->change_qty }}</td><td><span class="badge bg-soft-info text-info">{{ str_replace('_', ' ', $row->transaction_type) }}</span></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Serial Trace (Full Width) -->
            <div class="col-12 mt-4">
                <div id="card-serial" class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-dark fw-bold">Physical Unit (Serial) Trace</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.stock', array_merge(request()->all(), ['view' => 'serial'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-serial', 'Serial Trace')"><i class="bx bx-printer"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Serial #</th><th>Product</th><th>Status</th><th class="text-end pe-3">Updated At</th></tr></thead>
                            <tbody>
                                @foreach($breakdowns['serial'] as $row)
                                    <tr><td class="ps-3 fw-bold text-primary">{{ $row->serial_no }}</td><td>{{ $row->product_name }}</td><td><span class="badge bg-soft-success text-success">{{ ucfirst($row->stock_status) }}</span></td><td class="text-end pe-3 small text-muted">{{ \Carbon\Carbon::parse($row->updated_at)->format('d M, Y') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
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
            // Hide non-essential elements for the full-page print
            $('.no-print, .btn-group, .btn, .bx, iconify-icon, .card-header, .card-footer, .pagination').hide();
            
            // Add a header for the print
            const bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
            const dateStr = new Date().toLocaleString();
            const reportTitle = "{{ $title ?? 'Stock Report' }}";
            
            $('body').prepend(`
                <div class="text-center mb-4 border-bottom pb-3">
                    <h1>${bName}</h1>
                    <h3>${reportTitle}</h3>
                    <p>Generated: ${dateStr}</p>
                </div>
            `);

            // Apply print styles
            $('<style>')
                .prop('type', 'text/css')
                .html('body{background:white !important; color:black !important; padding: 20px !important;} table{width:100% !important; border-collapse:collapse !important;} th,td{border:1px solid #ddd !important; padding:8px !important; font-size:12px !important;} .card{border:none !important; shadow:none !important;}')
                .appendTo('head');

            window.print();
        }
    });

    function printFullReport() {
        const url = new URL(window.location.href);
        url.searchParams.set('is_print', '1');
        window.open(url.toString(), '_blank');
    }

    function printReportCard(cardId, reportTitle) {
        var content = document.getElementById(cardId);
        if (!content) return;

        var clone = content.cloneNode(true);
        var extras = clone.querySelectorAll('.btn-group, .btn, .bx, iconify-icon');
        for (var i = 0; i < extras.length; i++) {
            extras[i].remove();
        }

        var printWin = window.open('', '_blank', 'width=1100,height=800');
        if (!printWin) {
            alert('Please allow popups to print reports.');
            return;
        }

        var bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        var dateStr = new Date().toLocaleString();

        printWin.document.title = 'Report - ' + reportTitle;
        
        var style = printWin.document.createElement('style');
        style.innerHTML = 'body{font-family:sans-serif;padding:40px;color:#000} .hdr{text-align:center;margin-bottom:30px;border-bottom:2px solid #000} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{border:1px solid #000 !important;padding:8px;text-align:left;font-size:12px;color:#000 !important} th{background-color:#f2f2f2 !important;font-weight:bold; -webkit-print-color-adjust: exact;} .text-end{text-align:right !important} .text-center{text-align:center !important} .fw-bold{font-weight:bold !important} .badge{border:1px solid #000; padding:2px 5px; border-radius:3px; font-size:10px}';
        printWin.document.head.appendChild(style);

        var header = printWin.document.createElement('div');
        header.className = 'hdr';
        header.innerHTML = '<h1>' + bName + '</h1><h3>Stock Report: ' + reportTitle + '</h3><p>Generated: ' + dateStr + '</p>';
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
        console.log("Stock Report Dashboard Initialized");
    });
</script>
@endsection
