<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-3">#</th>
                <th>Return ID</th>
                <th>Product</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Condition</th>
                <th>Received Date</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($items); @endphp
            @forelse($items as $item)
                <tr>
                    <td class="ps-3">{{ $sl++ }}</td>
                    <td><span class="fw-bold">{{ $item->returnRequest->return_id }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->product->primaryImage ? asset('storage/'.$item->product->primaryImage->image_path) : asset('admin_assets/images/no-image.png') }}" class="rounded-pill" style="width: 35px; height: 35px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 fs-13">{{ $item->product->name }}</h6>
                                @if($item->productVariant)
                                    <small class="text-muted">{{ $item->productVariant->variant_name }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->condition === 'intact' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </td>
                    <td>{{ $item->updated_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No returned products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($items->hasPages())
    <div class="card-footer border-top-0">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted">
                Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} Results
            </div>
            {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
