@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-md-4">
            <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Total revenue from 'Delivered' orders today (after discounts)">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-medium text-truncate mb-2">Today's Revenue <i class="bx bx-info-circle small"></i></p>
                            <h4 class="mb-0 text-dark">{{ $gs->currency ?? '$' }}{{ number_format($summary['todaySales'], 2) }}</h4>
                        </div>
                        <div class="avatar-md bg-soft-primary rounded">
                            <i class="bx bx-dollar-circle avatar-title fs-24 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.orders.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d'), 'order_status' => 'Delivered']) }}" class="text-decoration-none">
                <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Total revenue from 'Delivered' orders this month">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">This Month's Revenue <i class="bx bx-info-circle small"></i></p>
                                <h4 class="mb-0 text-dark">{{ $gs->currency ?? '$' }}{{ number_format($summary['thisMonthSales'], 2) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-primary rounded">
                                <i class="bx bx-cart avatar-title fs-24 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.orders.index', ['date_from' => now()->startOfYear()->format('Y-m-d'), 'date_to' => now()->endOfYear()->format('Y-m-d'), 'order_status' => 'Delivered']) }}" class="text-decoration-none">
                <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Total revenue from 'Delivered' orders this year">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">This Year's Revenue <i class="bx bx-info-circle small"></i></p>
                                <h4 class="mb-0 text-dark">{{ $gs->currency ?? '$' }}{{ number_format($summary['thisYearSales'], 2) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <i class="bx bx-line-chart avatar-title fs-24 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Revenue minus Procurement Cost for today's delivered orders">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-medium text-truncate mb-2">Today's Profit <i class="bx bx-info-circle small"></i></p>
                            <h4 class="mb-0 text-dark">{{ $gs->currency ?? '$' }}{{ number_format($summary['todayProfit'], 2) }}</h4>
                        </div>
                        <div class="avatar-md bg-soft-success rounded">
                            <i class="bx bx-trending-up avatar-title fs-24 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Revenue minus Procurement Cost for this month's delivered orders">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-medium text-truncate mb-2">This Month's Profit <i class="bx bx-info-circle small"></i></p>
                            <h4 class="mb-0 text-dark">{{ $gs->currency ?? '$' }}{{ number_format($summary['thisMonthProfit'], 2) }}</h4>
                        </div>
                        <div class="avatar-md bg-soft-success rounded">
                            <i class="bx bx-money avatar-title fs-24 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Revenue minus Procurement Cost for this year's delivered orders">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-medium text-truncate mb-2">This Year's Profit <i class="bx bx-info-circle small"></i></p>
                            <h4 class="mb-0 text-dark">{{ $gs->currency ?? '$' }}{{ number_format($summary['thisYearProfit'], 2) }}</h4>
                        </div>
                        <div class="avatar-md bg-soft-success rounded">
                            <i class="bx bx-rocket avatar-title fs-24 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Total unique products currently in the system catalog">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Total Products <i class="bx bx-info-circle small"></i></p>
                                <h4 class="mb-0 text-dark">{{ number_format($summary['totalProducts']) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-primary rounded">
                                <i class="bx bx-box avatar-title fs-24 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d')]) }}" class="text-decoration-none">
                <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Total number of orders (all statuses) placed this month">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Orders This Month <i class="bx bx-info-circle small"></i></p>
                                <h4 class="mb-0 text-dark">{{ number_format($summary['thisMonthOrdersCount']) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <i class="bx bx-cart avatar-title fs-24 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.customers.index') }}" class="text-decoration-none">
                <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Total registered customers in the system">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Total Customers <i class="bx bx-info-circle small"></i></p>
                                <h4 class="mb-0 text-dark">{{ number_format($summary['totalCustomers']) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-info rounded">
                                <i class="bx bx-group avatar-title fs-24 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['order_status' => 'Pending']) }}" class="text-decoration-none">
                <div class="card" data-bs-toggle="tooltip" data-bs-placement="top" title="Orders currently awaiting processing or confirmation">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Pending Orders <i class="bx bx-info-circle small"></i></p>
                                <h4 class="mb-0 text-dark">{{ number_format($summary['pendingOrdersCount']) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-danger rounded">
                                <i class="bx bx-time avatar-title fs-24 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Monthly comparison of Gross Revenue vs Procurement Cost for delivered orders">Monthly Revenue vs Cost ({{ date('Y') }}) <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="revenue-cost-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Historical breakdown of annual revenue and procurement expenditure">Yearly Revenue vs Cost <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="yearly-revenue-cost-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Monthly trend of net profit calculated from delivered orders">Monthly Profit Review ({{ date('Y') }}) <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="monthly-profit-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Historical analysis of annual net profit growth">Yearly Profit Review <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="yearly-profit-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Monthly volume of customer orders (excluding cancelled/rejected)">Monthly Orders ({{ date('Y') }}) <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="monthly-orders-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Long-term trend of annual order volume">Yearly Orders Review <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="yearly-orders-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Monthly count of confirmed Purchase Orders from suppliers">Monthly Purchases ({{ date('Y') }}) <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="monthly-purchases-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="Annual growth of procurement activities">Yearly Purchases Review <i class="bx bx-info-circle small"></i></h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="yearly-purchases-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Monthly Best Sellers ({{ date('M') }})</h4>
                    <a href="{{ route('admin.products.best-selling', ['period' => 'monthly']) }}" class="btn btn-sm btn-soft-primary" data-bs-toggle="tooltip" title="View Monthly Best Sellers">
                        <i class="bx bx-show fs-14 me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <tbody>
                                @forelse($monthlyBestSellingProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($product->primaryImage?->image_path ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                                            <div>
                                                <h5 class="fs-14 my-1"><a href="{{ route('admin.products.show', $product->id) }}" class="text-reset">{{ $product->name }}</a></h5>
                                                <span class="text-muted fs-12">{{ $product->category?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal text-end">{{ number_format($product->period_sales_count) }}</h5>
                                        <p class="text-muted fs-12 mb-0 text-end">Sales</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-3 text-muted">No sales this month yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Yearly Best Sellers ({{ date('Y') }})</h4>
                    <a href="{{ route('admin.products.best-selling', ['period' => 'yearly']) }}" class="btn btn-sm btn-soft-primary" data-bs-toggle="tooltip" title="View Yearly Best Sellers">
                        <i class="bx bx-show fs-14 me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <tbody>
                                @forelse($yearlyBestSellingProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($product->primaryImage?->image_path ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                                            <div>
                                                <h5 class="fs-14 my-1"><a href="{{ route('admin.products.show', $product->id) }}" class="text-reset">{{ $product->name }}</a></h5>
                                                <span class="text-muted fs-12">{{ $product->category?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal text-end">{{ number_format($product->period_sales_count) }}</h5>
                                        <p class="text-muted fs-12 mb-0 text-end">Sales</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-3 text-muted">No sales this year yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title" data-bs-toggle="tooltip" title="List of products that have reached or dropped below their minimum stock thresholds">Low Stock Alerts <i class="bx bx-info-circle small"></i></h4>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.inventory.check-low-stock') }}" class="btn btn-sm btn-outline-danger">
                            <i class="bx bx-mail-send me-1"></i> Check & Notify Now
                        </a>
                        <span class="badge bg-soft-danger text-danger">Total Issues: {{ $summary['lowStockCount'] }}</span>
                        <a href="{{ route('admin.products.low-stock') }}" class="btn btn-sm btn-soft-primary" data-bs-toggle="tooltip" title="View All Low Stock Items">
                            <i class="bx bx-show fs-14 me-1"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th>Variant</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th class="text-center">Current Stock</th>
                                    <th class="text-center">Suggested Restock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($item['image'] ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                                            <div>
                                                <h5 class="fs-14 my-1 text-truncate" style="max-width: 200px;">
                                                    <a href="{{ route('admin.products.show', $item['product_id']) }}" class="text-reset">{{ $item['name'] }}</a>
                                                </h5>
                                                <span class="text-muted fs-12">{{ $item['category'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item['variant_name'] }}</td>
                                    <td>
                                        <span class="badge {{ $item['type'] === 'Global' ? 'bg-soft-primary text-primary' : 'bg-soft-warning text-warning' }}">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item['location'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-danger">{{ $item['stock'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">+{{ $item['suggested_restock'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
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
<script src="{{ asset('admin_assets/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('admin_assets/assets/js/pages/dashboard.js') }}"></script>
<script>
    // Monthly Revenue vs Cost Chart
    var revenueCostOptions = {
        series: [{
            name: 'Revenue',
            data: @json($revenueVsCostData['revenue'])
        }, {
            name: 'Cost',
            data: @json($revenueVsCostData['cost'])
        }, {
            name: 'Profit',
            data: @json($revenueVsCostData['profit'])
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: [3, 3, 2], dashArray: [0, 0, 5] },
        colors: ['#7f56da', '#ff6c2f', '#22c55e'],
        xaxis: { categories: @json($revenueVsCostData['labels']) },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.5,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#revenue-cost-chart"), revenueCostOptions).render();

    // Yearly Revenue vs Cost Chart
    var yearlyRevCostOptions = {
        series: [{
            name: 'Revenue',
            data: @json($yearlyRevenueVsCostData['revenue'])
        }, {
            name: 'Cost',
            data: @json($yearlyRevenueVsCostData['cost'])
        }, {
            name: 'Profit',
            data: @json($yearlyRevenueVsCostData['profit'])
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            },
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: { categories: @json($yearlyRevenueVsCostData['labels']) },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        },
        colors: ['#7f56da', '#ff6c2f', '#22c55e'],
        fill: { opacity: 1 },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#yearly-revenue-cost-chart"), yearlyRevCostOptions).render();

    // Monthly Profit Chart
    var monthlyProfitOptions = {
        series: [{
            name: 'Profit',
            data: @json($revenueVsCostData['profit'])
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#22c55e'],
        xaxis: { categories: @json($revenueVsCostData['labels']) },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#monthly-profit-chart"), monthlyProfitOptions).render();

    // Yearly Profit Chart
    var yearlyProfitOptions = {
        series: [{
            name: 'Profit',
            data: @json($yearlyRevenueVsCostData['profit'])
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '50%',
            }
        },
        colors: ['#22c55e'],
        xaxis: { categories: @json($yearlyRevenueVsCostData['labels']) },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "{{ $gs->currency ?? '$' }}" + value.toLocaleString();
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#yearly-profit-chart"), yearlyProfitOptions).render();

    // Monthly Orders Chart
    var monthlyOrdersOptions = {
        series: [{
            name: 'Orders',
            data: @json($monthlyOrderData['data'])
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false }
        },
        stroke: { width: 3, curve: 'smooth' },
        colors: ['#7f56da'],
        xaxis: { categories: @json($monthlyOrderData['labels']) },
        markers: { size: 4 },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value.toLocaleString() + " Orders";
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#monthly-orders-chart"), monthlyOrdersOptions).render();

    // Yearly Orders Chart
    var yearlyOrdersOptions = {
        series: [{
            name: 'Orders',
            data: @json($yearlyOrderData['data'])
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        colors: ['#7f56da'],
        xaxis: { categories: @json($yearlyOrderData['labels']) },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value.toLocaleString() + " Orders";
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#yearly-orders-chart"), yearlyOrdersOptions).render();

    // Monthly Purchases Chart
    var monthlyPurchasesOptions = {
        series: [{
            name: 'Purchases',
            data: @json($monthlyPurchaseData['data'])
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false }
        },
        stroke: { width: 3, curve: 'smooth' },
        colors: ['#108dff'],
        xaxis: { categories: @json($monthlyPurchaseData['labels']) },
        markers: { size: 4 },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value.toLocaleString() + " Purchases";
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#monthly-purchases-chart"), monthlyPurchasesOptions).render();

    // Yearly Purchases Chart
    var yearlyPurchasesOptions = {
        series: [{
            name: 'Purchases',
            data: @json($yearlyPurchaseData['data'])
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        colors: ['#108dff'],
        xaxis: { categories: @json($yearlyPurchaseData['labels']) },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value.toLocaleString() + " Purchases";
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#yearly-purchases-chart"), yearlyPurchasesOptions).render();
</script>
@endsection
