@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">All Low Stock Products</h4>
                    <span class="badge bg-soft-danger text-danger">Count: {{ $lowStockProducts->total() }}</span>
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
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $variant)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . ($variant->product?->primaryImage?->image_path ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                                            <div>
                                                <h5 class="fs-14 my-1">{{ $variant->product?->name ?? 'N/A' }}</h5>
                                                <span class="text-muted fs-12">{{ $variant->product?->category?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $variant->variant_name }}</td>
                                    <td>{{ $variant->sku }}</td>
                                    <td>
                                        <span class="fw-bold text-danger">{{ $variant->stock }}</span>
                                    </td>
                                    <td>
                                        {{ config('app.currency', '$') }}{{ number_format($variant->regular_price, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-danger text-danger">Low Stock</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="btn btn-sm btn-soft-primary">
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
                @if($lowStockProducts->hasPages())
                <div class="card-footer">
                    {{ $lowStockProducts->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
