@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Warehouse Performance</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm filter-card">
        <div class="card-body">
            <form action="{{ route('admin.reports.warehouse-performance') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Warehouse</label>
                    <select name="warehouse_id" class="form-select select2_list">
                        <option value="all">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Efficiency & Quality Metrics</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-soft-secondary" onclick="window.print()">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Warehouse</th>
                            <th class="text-center">Closing Stock</th>
                            <th class="text-center">Fill Rate</th>
                            <th class="text-center">Damage Rate</th>
                            <th class="text-center">Stock Turnover</th>
                            <th class="text-center">Slow SKUs %</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $row['warehouse_name'] }}</td>
                                <td class="text-center fw-medium">{{ number_format($row['closing_stock']) }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress w-50 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $row['fill_rate'] }}%"></div>
                                        </div>
                                        <span class="small fw-bold">{{ number_format($row['fill_rate'], 1) }}%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $row['damage_rate'] > 5 ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }}">
                                        {{ number_format($row['damage_rate'], 2) }}%
                                    </span>
                                </td>
                                <td class="text-center fw-bold">{{ number_format($row['stock_turnover'], 2) }}x</td>
                                <td class="text-center text-muted">{{ number_format($row['slow_moving_percent'], 1) }}%</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.reports.warehouse-performance.show', [$row['warehouse_id'], 'start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" 
                                       class="btn btn-sm btn-soft-primary">
                                        <i class="bx bx-show me-1"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No data found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $reportData->links() }}
        </div>
    </div>
</div>
@endsection
