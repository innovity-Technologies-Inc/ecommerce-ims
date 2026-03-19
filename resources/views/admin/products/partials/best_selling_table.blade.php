<div class="table-responsive">
    <table class="table table-hover table-centered mb-0">
        <thead class="bg-light bg-opacity-50">
            <tr>
                <th class="ps-3">#</th>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th class="text-end">Sales Qty</th>
                <th class="text-end pe-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td class="ps-3">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('storage/' . ($product->primaryImage?->image_path ?? '')) }}" alt="" class="avatar-sm rounded me-2">
                        <h5 class="fs-14 my-1"><a href="{{ route('admin.products.show', $product->id) }}" class="text-reset">{{ $product->name }}</a></h5>
                    </div>
                </td>
                <td>{{ $product->category?->name ?? 'N/A' }}</td>
                <td>{{ $product->brand?->name ?? 'N/A' }}</td>
                <td class="text-end fw-bold">{{ number_format($product->period_sales_count) }}</td>
                <td class="text-end pe-3">
                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-soft-primary"><i class="bx bx-show"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">No best selling products found for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
<div class="card-footer border-top-0 bg-transparent">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <p class="mb-0 text-muted">Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} Results</p>
        </div>
        <div class="pagination-container">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif
