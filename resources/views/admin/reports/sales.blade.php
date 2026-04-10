@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Sales Reports</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card no-print">
        <div class="card-body">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3">
                <input type="hidden" name="view" value="{{ $view ?? '' }}">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Group By</label>
                    <select name="group_by" class="form-select">
                        <option value="daily" {{ ($filters['group_by'] ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ ($filters['group_by'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ ($filters['group_by'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ ($filters['group_by'] ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
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
                    <label class="form-label small fw-bold">Brand</label>
                    <select name="brand_id" class="form-select select2_list">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ ($filters['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
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
                    <div class="collapse {{ !empty($filters['product_id']) || !empty($filters['category_id']) || !empty($filters['order_status']) ? 'show' : '' }} mt-3" id="extraFilters">
                        <div class="row g-3">
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
                                <label class="form-label small fw-bold">Product</label>
                                <select name="product_id" class="form-select select2_list">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ ($filters['product_id'] ?? '') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Order Status</label>
                                <select name="order_status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @php $statuses = ['Pending', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled', 'Rejected']; @endphp
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ ($filters['order_status'] ?? '') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">All Methods</option>
                                    <option value="COD" {{ ($filters['payment_method'] ?? '') == 'COD' ? 'selected' : '' }}>Cash on Delivery</option>
                                    <option value="Online" {{ ($filters['payment_method'] ?? '') == 'Online' ? 'selected' : '' }}>Online Pay</option>
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
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-primary rounded">
                                <i class="bx bx-dollar fs-24 text-primary mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Net Sales</h6>
                            <h3 class="mb-0 fw-bold text-primary">${{ number_format($summary['totals']['net_sales'], 2) }}</h3>
                            <div class="small text-muted">Gross: ${{ number_format($summary['totals']['gross_sales'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-soft-success rounded">
                                <i class="bx bx-trending-up fs-24 text-success mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Gross Profit</h6>
                            <h3 class="mb-0 fw-bold text-success">${{ number_format($summary['totals']['gross_profit'], 2) }}</h3>
                            <div class="small text-muted">Margin: {{ number_format($summary['totals']['gross_margin_percent'], 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-sm bg-soft-info rounded">
                                <i class="bx bx-calculator fs-20 text-info mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">AOV</h6>
                            <h3 class="mb-0 fw-bold">${{ number_format($summary['totals']['aov'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-sm bg-soft-warning rounded">
                                <i class="bx bx-cart fs-20 text-warning mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Orders</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($summary['totals']['orders_count']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-sm bg-soft-danger rounded">
                                <i class="bx bx-package fs-20 text-danger mt-2 ms-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted small text-uppercase mb-1">Units</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($summary['totals']['units_sold']) }}</h3>
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
                    <a href="{{ route('admin.reports.sales', request()->except('view', 'page')) }}" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bx bx-arrow-back"></i> Dashboard
                    </a>
                    <h5 class="card-title mb-0 d-inline-block">{{ $title }}</h5>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.reports.sales.export', array_merge(request()->all(), ['type' => $view === 'trends' ? 'trends' : $view])) }}" class="btn btn-sm btn-soft-success">
                        <i class="bx bx-download me-1"></i> Export
                    </a>
                    <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printFullReport()">
                        <i class="bx bx-printer"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0" id="detailed-table-container">
                <div class="table-responsive">
                    @if($view === 'trends')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Period</th>
                                    <th class="text-center">Orders</th>
                                    <th class="text-end">Net Sales</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end pe-3">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $row->period }}</td>
                                        <td class="text-center">{{ $row->orders_count }}</td>
                                        <td class="text-end text-primary fw-bold">${{ number_format($row->net_sales, 2) }}</td>
                                        <td class="text-end text-muted small">${{ number_format($row->total_cost, 2) }}</td>
                                        <td class="text-end text-success fw-bold pe-3">${{ number_format($row->gross_profit, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($view === 'payment_method')
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Method</th>
                                    <th class="text-center">Orders</th>
                                    <th class="text-end pe-3">Net Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $row->name }}</td>
                                        <td class="text-center">{{ $row->orders_count }}</td>
                                        <td class="text-end pe-3 fw-bold">${{ number_format($row->net_sales, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        {{-- Product, Variant, Warehouse, Batch --}}
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">{{ ucfirst($view) }}</th>
                                    <th class="text-center">Units Sold</th>
                                    <th class="text-end">Net Sales</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end pe-3">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $row->name }}</td>
                                        <td class="text-center">{{ number_format($row->units_sold) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($row->net_sales, 2) }}</td>
                                        <td class="text-end text-muted small">${{ number_format($row->total_cost, 2) }}</td>
                                        <td class="text-end text-success fw-bold pe-3">${{ number_format($row->gross_profit, 2) }}</td>
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
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <!-- Grouped Data Table -->
                <div id="card-trends" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales Trends ({{ ucfirst($summary['group_by']) }})</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['view' => 'trends'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.sales.export', array_merge(request()->all(), ['type' => 'trends'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-trends', 'Sales Trends')">
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
                                        <th class="ps-3">Period</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Net Sales</th>
                                        <th class="text-end">Cost</th>
                                        <th class="text-end pe-3">Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($summary['grouped_data']->take(10) as $data)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $data->period }}</td>
                                            <td class="text-center">{{ $data->orders_count }}</td>
                                            <td class="text-end text-primary fw-bold">${{ number_format($data->net_sales, 2) }}</td>
                                            <td class="text-end text-muted small">${{ number_format($data->total_cost, 2) }}</td>
                                            <td class="text-end text-success fw-bold pe-3">${{ number_format($data->gross_profit, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">No sales data found for this period.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Summary Table -->
                <div id="card-financial" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Financial Summary</h5>
                        <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-financial', 'Financial Summary')">
                            <i class="bx bx-printer"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="ps-3 py-3">Gross Sales</td>
                                    <td class="text-end pe-3 py-3 fw-bold">${{ number_format($summary['totals']['gross_sales'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-3">Discounts</td>
                                    <td class="text-end pe-3 py-3 text-danger fw-bold">-${{ number_format($summary['totals']['discount_amount'], 2) }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="ps-3 py-3 fw-bold text-primary">Net Sales</td>
                                    <td class="text-end pe-3 py-3 fw-bold text-primary">${{ number_format($summary['totals']['net_sales'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-3">Shipping Revenue</td>
                                    <td class="text-end pe-3 py-3 fw-bold">${{ number_format($summary['totals']['shipping_revenue'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-3">Total Cost (COGS)</td>
                                    <td class="text-end pe-3 py-3 text-muted fw-bold">-${{ number_format($summary['totals']['total_cost'], 2) }}</td>
                                </tr>
                                <tr class="bg-soft-success">
                                    <td class="ps-3 py-3 fw-bold text-success">Gross Profit</td>
                                    <td class="text-end pe-3 py-3 fw-bold text-success">${{ number_format($summary['totals']['gross_profit'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-3">Gross Margin %</td>
                                    <td class="text-end pe-3 py-3 fw-bold">{{ number_format($summary['totals']['gross_margin_percent'], 2) }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div id="card-product" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Top Products by Sales</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['view' => 'product'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.sales.export', array_merge(request()->all(), ['type' => 'product'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-product', 'Top Products')">
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
                                        <th class="text-center">Sold</th>
                                        <th class="text-end pe-3">Net Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breakdowns['product']->take(10) as $prod)
                                        <tr>
                                            <td class="ps-3 small">{{ $prod->name }}</td>
                                            <td class="text-center">{{ $prod->units_sold }}</td>
                                            <td class="text-end pe-3 fw-bold">${{ number_format($prod->net_sales, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div id="card-warehouse" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales by Warehouse</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['view' => 'warehouse'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.sales.export', array_merge(request()->all(), ['type' => 'warehouse'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-warehouse', 'Warehouse Sales')">
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
                                        <th class="text-center">Sold</th>
                                        <th class="text-end pe-3">Net Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breakdowns['warehouse']->take(10) as $wh)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $wh->name }}</td>
                                            <td class="text-center">{{ $wh->units_sold }}</td>
                                            <td class="text-end pe-3 fw-bold">${{ number_format($wh->net_sales, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div id="card-payment" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payment Methods Breakdown</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['view' => 'payment_method'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.sales.export', array_merge(request()->all(), ['type' => 'payment_method'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-payment', 'Payment Methods')">
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
                                        <th class="ps-3">Method</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end pe-3">Net Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breakdowns['payment_method']->take(10) as $pm)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $pm->name }}</td>
                                            <td class="text-center">{{ $pm->orders_count }}</td>
                                            <td class="text-end pe-3 fw-bold">${{ number_format($pm->net_sales, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div id="card-batch" class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Top Batches by Sales</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.sales', array_merge(request()->all(), ['view' => 'batch'])) }}" class="btn btn-sm btn-soft-primary">View All</a>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.sales.export', array_merge(request()->all(), ['type' => 'batch'])) }}" class="btn btn-sm btn-soft-success">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="printReportCard('card-batch', 'Batch Sales')">
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
                                        <th class="ps-3">Batch #</th>
                                        <th class="text-center">Sold</th>
                                        <th class="text-end pe-3">Net Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breakdowns['batch']->take(10) as $batch)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $batch->name }}</td>
                                            <td class="text-center">{{ $batch->units_sold }}</td>
                                            <td class="text-end pe-3 fw-bold">${{ number_format($batch->net_sales, 2) }}</td>
                                        </tr>
                                    @endforeach
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
            const reportTitle = "{{ $title ?? 'Sales Report' }}";
            
            printContainer.append(`
                <div class="text-center mb-4 border-bottom pb-3">
                    <h1 class="fw-bold mb-1">${bName}</h1>
                    <h3 class="mb-2">${reportTitle}</h3>
                    <p class="mb-0 text-muted">Generated on: ${dateStr}</p>
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
        var btnGroups = clone.querySelectorAll('.btn-group, .btn, .bx, iconify-icon');
        for (var i = 0; i < btnGroups.length; i++) {
            btnGroups[i].style.display = 'none';
        }

        var printWin = window.open('', '_blank', 'width=1000,height=800');
        if (!printWin) {
            alert('Please allow popups');
            return;
        }

        var bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        var dateStr = new Date().toLocaleString();

        printWin.document.title = 'Report - ' + reportTitle;
        
        var style = printWin.document.createElement('style');
        style.innerHTML = 'body{font-family:sans-serif;padding:40px} .hdr{text-align:center;margin-bottom:30px;border-bottom:2px solid #000} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{border:1px solid #000;padding:10px;text-align:left;font-size:13px} th{background:#f2f2f2;font-weight:bold} .text-end{text-align:right} .text-center{text-align:center}';
        printWin.document.head.appendChild(style);

        var header = printWin.document.createElement('div');
        header.className = 'hdr';
        header.innerHTML = '<h1>' + bName + '</h1><h3>Sales Report: ' + reportTitle + '</h3><p>Generated: ' + dateStr + '</p>';
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
