<div class="table-responsive">
    <table class="table table-hover table-centered mb-0">
        <thead class="bg-light bg-opacity-50">
            <tr>
                <th class="ps-3">Product</th>
                <th>Variant</th>
                <th>Type</th>
                <th>Location</th>
                <th class="text-center">Current Stock</th>
                <th class="text-center">Min. Stock</th>
                <th class="text-center">Suggested Restock</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lowStockProducts as $item)
            @php $item = (array)$item; @endphp
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
                    <span class="text-muted">{{ $item['min_stock'] ?? 'N/A' }}</span>
                </td>
                <td class="text-center">
                    <span class="fw-bold text-success">+{{ max(($item['min_stock'] ?? 10) * 2 - $item['stock'], 10) }}</span>
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

<div class="card-footer border-top">
    {{ $lowStockProducts->appends(request()->all())->links() }}
</div>
