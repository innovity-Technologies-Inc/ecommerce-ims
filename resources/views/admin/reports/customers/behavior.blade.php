@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.reports.customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0">Purchase Behavior Analytics</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('admin.reports.customers.behavior') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Status Distribution -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order Status Distribution</h5>
                    <i class="bx bx-info-circle text-muted" data-bs-toggle="tooltip" title="Percentage breakdown of all orders by their current status (Delivered, Pending, Cancelled, etc.)"></i>
                </div>
                <div class="card-body">
                    <div id="orderStatusChart"></div>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th class="text-center">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($behavior['status_distribution'] as $status)
                                    <tr>
                                        <td class="text-capitalize">{{ $status->order_status }}</td>
                                        <td class="text-center fw-bold">{{ $status->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- AOV Trend -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Average Order Value (AOV) Trend</h5>
                    <i class="bx bx-info-circle text-muted" data-bs-toggle="tooltip" title="Historical trend showing how much customers spend on average per order month-over-month."></i>
                </div>
                <div class="card-body">
                    <div id="aovTrendChart"></div>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">AOV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($behavior['aov_trend'] as $trend)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trend->month . '-01')->format('M Y') }}</td>
                                        <td class="text-end fw-bold">${{ number_format($trend->aov, 2) }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Order Status Chart
    const statusData = @json($behavior['status_distribution']->pluck('count'));
    const statusLabels = @json($behavior['status_distribution']->pluck('order_status'));
    
    new ApexCharts(document.querySelector("#orderStatusChart"), {
        series: statusData,
        chart: {
            type: 'donut',
            height: 300
        },
        labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        colors: ['#0acf97', '#727cf5', '#ffbc00', '#fa5c7c', '#39afd1'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    }).render();

    // AOV Trend Chart
    const aovData = @json($behavior['aov_trend']->pluck('aov'));
    const aovMonths = @json($behavior['aov_trend']->pluck('month'));

    new ApexCharts(document.querySelector("#aovTrendChart"), {
        series: [{
            name: 'Average Order Value',
            data: aovData
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false }
        },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#727cf5'],
        xaxis: {
            categories: aovMonths.map(m => {
                const [y, mm] = m.split('-');
                return new Date(y, mm - 1).toLocaleString('default', { month: 'short' });
            })
        },
        yaxis: {
            labels: {
                formatter: (val) => '$' + val.toFixed(0)
            }
        }
    }).render();
</script>
@endsection
