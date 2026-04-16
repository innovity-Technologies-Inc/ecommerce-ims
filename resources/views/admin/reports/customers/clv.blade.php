@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.reports.customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0">Customer Lifetime Value (CLV) Projections</h4>
        </div>
    </div>

    <!-- Explanation -->
    <div class="alert alert-soft-info border-0 shadow-sm mb-4">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                <i class="bx bx-trending-up fs-24 mt-1"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">Predictive CLV Methodology</h6>
                <p class="mb-0 small">The <strong>Total CLV</strong> is calculated as: <code>Historical Spend + (AOV × Monthly Purchase Frequency × 24 Month Lifespan)</code>. This helps identify high-potential customers even if their current spend is low.</p>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-primary border-4 h-100" data-bs-toggle="tooltip" title="Average predicted lifetime value across all customers based on historical spend and purchase frequency.">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase mb-1">Average Projected CLV <i class="bx bx-info-circle small"></i></h6>
                    <h3 class="mb-0 fw-bold text-primary">${{ number_format($clv['averages']['avg_clv'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-4 border-end" data-bs-toggle="tooltip" title="High-value customers with a projected lifetime value exceeding $2,000.">
                            <h6 class="text-muted extra-small mb-1">Whales <i class="bx bx-info-circle extra-small"></i></h6>
                            <h5 class="mb-0 fw-bold text-success">{{ $clv['segments']['whales'] }}</h5>
                        </div>
                        <div class="col-4 border-end" data-bs-toggle="tooltip" title="Customers with a projected lifetime value between $500 and $2,000.">
                            <h6 class="text-muted extra-small mb-1">Medium <i class="bx bx-info-circle extra-small"></i></h6>
                            <h5 class="mb-0 fw-bold text-info">{{ $clv['segments']['medium'] }}</h5>
                        </div>
                        <div class="col-4" data-bs-toggle="tooltip" title="Standard customers with a projected lifetime value of $500 or less.">
                            <h6 class="text-muted extra-small mb-1">Standard <i class="bx bx-info-circle extra-small"></i></h6>
                            <h5 class="mb-0 fw-bold text-secondary">{{ $clv['segments']['standard'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-warning border-4 h-100" data-bs-toggle="tooltip" title="The average amount customers have actually spent to date (Total Sales / Total Customers).">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase mb-1">Avg Historical Value <i class="bx bx-info-circle small"></i></h6>
                    <h3 class="mb-0 fw-bold text-warning">${{ number_format($clv['averages']['avg_historical'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Top CLV Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title mb-0">Top 20 Customers by Projected Lifetime Value</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Customer</th>
                            <th class="text-end">Historical Spend</th>
                            <th class="text-end">AOV</th>
                            <th class="text-center">Freq (Monthly)</th>
                            <th class="text-end text-primary">Projected (24M)</th>
                            <th class="text-end fw-bold text-dark">Total CLV</th>
                            <th class="text-center">Tier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clv['top_customers'] as $row)
                            <tr>
                                <td class="ps-3">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $row['name'] }}</h6>
                                    <small class="text-muted">{{ $row['email'] }}</small>
                                </td>
                                <td class="text-end">${{ number_format($row['historical_value'], 2) }}</td>
                                <td class="text-end small">${{ number_format($row['aov'], 2) }}</td>
                                <td class="text-center small">{{ $row['frequency'] }} orders</td>
                                <td class="text-end text-primary small">${{ number_format($row['projected_value'], 2) }}</td>
                                <td class="text-end fw-bold text-dark">${{ number_format($row['total_clv'], 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        if($row['status'] == 'High Value (Whale)') $badgeClass = 'bg-primary';
                                        elseif($row['status'] == 'Medium Value') $badgeClass = 'bg-info';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $row['status'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
