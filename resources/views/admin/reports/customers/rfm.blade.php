@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.reports.customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0">RFM Analysis & Segmentation</h4>
        </div>
    </div>

    <!-- RFM Explanation -->
    <div class="alert alert-soft-primary border-0 shadow-sm mb-4">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                <i class="bx bx-info-circle fs-24 mt-1"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">About RFM Analysis</h6>
                <p class="mb-0 small">RFM (Recency, Frequency, Monetary) analysis is a marketing technique used to determine quantitatively which customers are the best ones by examining how recently a customer has purchased (recency), how often they purchase (frequency), and how much the customer spends (monetary).</p>
            </div>
        </div>
    </div>

    <!-- Segment Distribution -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-top border-primary border-4 h-100" data-bs-toggle="tooltip" title="High value customers who purchase frequently and have been active recently.">
                <div class="card-body text-center">
                    <h1 class="fw-bold text-primary">{{ count($rfm['segments']['VIP']) }}</h1>
                    <h6 class="text-muted text-uppercase small fw-bold">VIP Customers <i class="bx bx-info-circle small"></i></h6>
                    <p class="text-muted extra-small mb-0">High spend, high frequency, recent activity.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-top border-success border-4 h-100" data-bs-toggle="tooltip" title="Customers who buy regularly and are consistent in their purchase behavior.">
                <div class="card-body text-center">
                    <h1 class="fw-bold text-success">{{ count($rfm['segments']['Loyal']) }}</h1>
                    <h6 class="text-muted text-uppercase small fw-bold">Loyal Customers <i class="bx bx-info-circle small"></i></h6>
                    <p class="text-muted extra-small mb-0">Frequent buyers with consistent activity.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-top border-warning border-4 h-100" data-bs-toggle="tooltip" title="Customers who haven't made a purchase in 90 to 180 days and may need re-engagement.">
                <div class="card-body text-center">
                    <h1 class="fw-bold text-warning">{{ count($rfm['segments']['At Risk']) }}</h1>
                    <h6 class="text-muted text-uppercase small fw-bold">At Risk <i class="bx bx-info-circle small"></i></h6>
                    <p class="text-muted extra-small mb-0">Haven't purchased in 90-180 days.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-top border-danger border-4 h-100" data-bs-toggle="tooltip" title="Customers who have not been active for more than 180 days.">
                <div class="card-body text-center">
                    <h1 class="fw-bold text-danger">{{ count($rfm['segments']['Lost']) }}</h1>
                    <h6 class="text-muted text-uppercase small fw-bold">Lost Customers <i class="bx bx-info-circle small"></i></h6>
                    <p class="text-muted extra-small mb-0">Inactive for more than 180 days.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RFM Data Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="card-title mb-0">Customer RFM Scores</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Customer Name</th>
                            <th class="text-center">Recency (Days)</th>
                            <th class="text-center">Frequency (Orders)</th>
                            <th class="text-end">Monetary (Total)</th>
                            <th class="text-center">Potential Segment</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rfm['stats']->sortByDesc('monetary')->take(50) as $stat)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $stat['name'] }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $stat['recency'] <= 30 ? 'bg-soft-success text-success' : ($stat['recency'] <= 90 ? 'bg-soft-warning text-warning' : 'bg-soft-danger text-danger') }} px-3">
                                        {{ $stat['recency'] }} Days
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-info text-info px-3">{{ $stat['frequency'] }}</span>
                                </td>
                                <td class="text-end fw-bold">{{ $gs->currency ?? '$' }}{{ number_format($stat['monetary'], 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $segment = 'Others';
                                        if($stat['recency'] <= 30 && $stat['frequency'] >= 5 && $stat['monetary'] >= 1000) $segment = 'VIP';
                                        elseif($stat['recency'] <= 60 && $stat['frequency'] >= 3) $segment = 'Loyal';
                                        elseif($stat['recency'] > 90 && $stat['recency'] <= 180) $segment = 'At Risk';
                                        elseif($stat['recency'] > 180) $segment = 'Lost';
                                    @endphp
                                    <span class="badge {{ $segment == 'VIP' ? 'bg-primary' : ($segment == 'Loyal' ? 'bg-success' : ($segment == 'At Risk' ? 'bg-warning' : ($segment == 'Lost' ? 'bg-danger' : 'bg-secondary'))) }}">
                                        {{ $segment }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.customers.show', $stat['user_id']) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="View Customer">
                                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
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
