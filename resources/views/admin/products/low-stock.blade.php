@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">All Low Stock Products (Global & Warehouse)</h4>
                    <span class="badge bg-soft-danger text-danger">Total Alerts: {{ count($lowStockProducts) }}</span>
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
                                @forelse($lowStockProducts as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($item['image'] ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                                            <div>
                                                <h5 class="fs-14 my-1">
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
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No low stock products found.</td>
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
