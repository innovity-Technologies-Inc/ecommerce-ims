<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Batch Number</th>
                <th>PO Number</th>
                <th>Warehouse</th>
                <th class="text-center">Total Items</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
                <tr>
                    <td>{{ \App\HelperClass::indexNumberSerialization($batches)[$loop->index] }}</td>
                    <td><code>{{ $batch->batch_number }}</code></td>
                    <td>
                        @if($batch->purchaseOrder)
                            <a href="{{ route('admin.inventory.po.show', $batch->purchase_order_id) }}" class="fw-bold text-primary">
                                {{ $batch->purchaseOrder->po_number }}
                            </a>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $batch->warehouse->is_quarantine ? 'bg-danger' : 'badge-soft-success' }}">
                            {{ $batch->warehouse->name }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-dark fs-13">{{ $batch->items->count() }}</span>
                    </td>
                    <td>
                        {{ $batch->created_at->format('M d, Y H:i') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.inventory.batches.show', $batch->id) }}" class="btn btn-soft-info btn-sm">
                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon> View Items
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-4">
                        <iconify-icon icon="solar:box-minimalistic-broken" class="fs-48 text-muted mb-2"></iconify-icon>
                        <p class="text-muted">No batches found matching your criteria.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            Showing <span class="fw-semibold">{{ $batches->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $batches->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $batches->total() }}</span> Results
        </div>
        <div>
            {{ $batches->appends(request()->all())->links() }}
        </div>
    </div>
</div>
