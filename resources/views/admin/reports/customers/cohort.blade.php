@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.reports.customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0">Customer Cohort Analysis (Retention Heatmap)</h4>
        </div>
    </div>

    <!-- Explanation -->
    <div class="alert alert-soft-info border-0 shadow-sm mb-4">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                <i class="bx bx-info-circle fs-24 mt-1"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">How to read this heatmap?</h6>
                <p class="mb-0 small">This table shows the retention rate of customers grouped by the month they registered (Cohort). Month 0 is their registration month (100%), and subsequent columns show the percentage of those same users who placed an order in the following months.</p>
            </div>
        </div>
    </div>

    <!-- Cohort Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-start">Cohort Month</th>
                            <th style="width: 100px;">Customers</th>
                            @for($i = 0; $i < 6; $i++)
                                <th>Month {{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cohorts as $cohort)
                            <tr>
                                <td class="ps-3 text-start fw-bold">
                                    {{ \Carbon\Carbon::parse($cohort['cohort'] . '-01')->format('M Y') }}
                                </td>
                                <td class="fw-bold bg-light">{{ $cohort['total'] }}</td>
                                @for($i = 0; $i < 6; $i++)
                                    @php
                                        $retention = $cohort['retention'][$i] ?? null;
                                        $percentage = $retention['percentage'] ?? 0;
                                        
                                        // Dynamic color based on percentage
                                        $opacity = $percentage / 100;
                                        $bgColor = "rgba(114, 124, 245, $opacity)";
                                        $textColor = $percentage > 50 ? '#fff' : '#000';
                                    @endphp
                                    <td style="background-color: {{ $bgColor }}; color: {{ $textColor }}; transition: all 0.3s;" 
                                        data-bs-toggle="tooltip" 
                                        title="{{ $retention ? $retention['count'] . ' active users' : 'No data' }}">
                                        @if($retention)
                                            <span class="fw-bold">{{ $percentage }}%</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
