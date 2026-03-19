@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d'), 'order_status' => 'Delivered']) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">This Month's Sales</p>
                                <h4 class="mb-0 text-dark">{{ config('app.currency', '$') }}{{ number_format($summary['thisMonthSales'], 2) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-primary rounded">
                                <i class="bx bx-dollar-circle avatar-title fs-24 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['date_from' => now()->startOfYear()->format('Y-m-d'), 'date_to' => now()->endOfYear()->format('Y-m-d'), 'order_status' => 'Delivered']) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">This Year's Sales</p>
                                <h4 class="mb-0 text-dark">{{ config('app.currency', '$') }}{{ number_format($summary['thisYearSales'], 2) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-success rounded">
                                <i class="bx bx-line-chart avatar-title fs-24 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['order_status' => 'Delivered']) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Total Sales Amount</p>
                                <h4 class="mb-0 text-dark">{{ config('app.currency', '$') }}{{ number_format($summary['totalSalesAmount'], 2) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-info rounded">
                                <i class="bx bx-wallet avatar-title fs-24 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['order_status' => 'Delivered']) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Product Sales Number</p>
                                <h4 class="mb-0 text-dark">{{ number_format($summary['totalProductSalesCount']) }}</h4>
                            </div>
                            <div class="avatar-md bg-soft-warning rounded">
                                <i class="bx bx-shopping-bag avatar-title fs-24 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Total Products</p>
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
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Orders This Month</p>
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
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Total Customers</p>
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
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted fw-medium text-truncate mb-2">Pending Orders</p>
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

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Monthly Sales Review ({{ date('Y') }})</h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="sales-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Yearly Sales Review</h4>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="yearly-sales-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Best Selling Products</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <tbody>
                                @foreach($bestSellingProducts as $product)
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
                                        <h5 class="fs-14 my-1 fw-normal text-end">{{ number_format($product->sales_count) }}</h5>
                                        <p class="text-muted fs-12 mb-0 text-end">Sales</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Low Stock Products</h4>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-soft-danger text-danger">Total: {{ $summary['lowStockCount'] }}</span>
                        <a href="{{ route('admin.products.low-stock') }}" class="btn btn-sm btn-soft-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th>Variant</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $variant)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($variant->product?->primaryImage?->image_path ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                                            <div>
                                                <h5 class="fs-14 my-1 text-truncate" style="max-width: 150px;">{{ $variant->product?->name ?? 'N/A' }}</h5>
                                                <span class="text-muted fs-12">{{ $variant->product?->category?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $variant->variant_name }}</td>
                                    <td><small class="text-muted">{{ $variant->sku }}</small></td>
                                    <td>
                                        <span class="fw-bold text-danger">{{ $variant->stock }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="btn btn-sm btn-soft-primary">Restock</a>
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
<script>
    // Monthly Chart
    var options = {
        series: [{
            name: "Sales",
            data: @json($chartData['data'])
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: @json($chartData['labels']),
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "{{ config('app.currency', '$') }}" + value.toLocaleString();
                }
            }
        },
        colors: ['#7f56da'],
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
                    return "{{ config('app.currency', '$') }}" + value.toLocaleString();
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
    chart.render();

    // Yearly Chart
    var yearlyOptions = {
        series: [{
            name: "Yearly Sales",
            data: @json($yearlyChartData['data'])
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '40%',
                borderRadius: 4,
                distributed: true
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: @json($yearlyChartData['labels']),
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "{{ config('app.currency', '$') }}" + value.toLocaleString();
                }
            }
        },
        colors: ['#7f56da', '#108dff', '#22c55e', '#ff6c2f', '#f5b759'],
        tooltip: {
            y: {
                formatter: function (value) {
                    return "{{ config('app.currency', '$') }}" + value.toLocaleString();
                }
            }
        },
        legend: {
            show: false
        }
    };

    var yearlyChart = new ApexCharts(document.querySelector("#yearly-sales-chart"), yearlyOptions);
    yearlyChart.render();
</script>
@endsection
