<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Batch Number</th>
                <th>Supplier</th>
                <th>Warehouse</th>
                <th class="text-center">Total Received</th>
                <th class="text-center">Saleable</th>
                <th class="text-center">Damaged</th>
                <th>Date Received</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($batches); @endphp
            @forelse($batches as $batch)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>
                        <code class="fw-bold fs-14">{{ $batch->batch_number }}</code>
                        @if($batch->purchaseOrder)
                            <br><small class="text-muted">PO: {{ $batch->purchaseOrder->po_number }}</small>
                        @endif
                    </td>
                    <td>{{ $batch->supplier->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-soft-info">{{ $batch->warehouse->name }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-dark fs-13">{{ $batch->total_received_qty }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-success fs-13">{{ $batch->total_saleable_qty }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-danger fs-13">{{ $batch->total_damaged_qty }}</span>
                    </td>
                    <td>
                        <small class="text-muted">{{ $batch->created_at->format('M d, Y H:i') }}</small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.inventory.batches.show', $batch->id) }}" class="btn btn-soft-info btn-sm">
                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Details
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center p-4">
                        <iconify-icon icon="solar:box-minimalistic-broken" class="fs-48 text-muted mb-2"></iconify-icon>
                        <p class="text-muted">No batch records found matching your criteria.</p>
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
