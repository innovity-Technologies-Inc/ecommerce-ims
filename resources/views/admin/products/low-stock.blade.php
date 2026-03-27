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
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
                                        <span class="badge {{ $item['type'] === 'Global' ? 'badge-soft-primary' : 'badge-soft-warning' }}">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item['location'] }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-danger">{{ $item['stock'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-danger text-danger">Low Stock</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $item['product_id']) }}" class="btn btn-sm btn-soft-primary">
                                            <i class="bx bx-edit-alt"></i> Restock
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No low stock products found.</td>
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
