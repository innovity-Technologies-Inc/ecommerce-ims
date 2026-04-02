<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Warehouse / Batch</th>
                <th>Serial Number</th>
                <th>Type / Reason</th>
                <th>Ref ID</th>
                <th class="text-center">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $ledger)
                <tr>
                    <td class="small">
                        {{ $ledger->created_at->format('M d, Y') }}<br>
                        <span class="text-muted">{{ $ledger->created_at->format('h:i A') }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($ledger->product->primaryImage)
                                <img src="{{ asset($ledger->product->primaryImage->image_path) }}" class="avatar-sm rounded me-2" alt="">
                            @else
                                <div class="avatar-sm rounded bg-soft-secondary d-flex align-items-center justify-content-center me-2">
                                    <i class="bx bx-package fs-20 text-secondary"></i>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('admin.products.show', $ledger->product_id) }}" class="fw-bold text-dark">
                                    {{ $ledger->product->name }}
                                </a>
                                @if($ledger->variant)
                                    <br><small class="text-muted">{{ $ledger->variant->variant_name }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium text-dark">{{ $ledger->warehouse->name ?? 'Unallocated Pool' }}</span><br>
                        <small class="text-muted">Batch: {{ $ledger->batch->batch_number ?? 'N/A' }}</small>
                    </td>
                    <td>
                        @if($ledger->serial)
                            <span class="badge bg-soft-info text-info border border-info px-2 py-1">
                                <i class="bx bx-barcode-reader me-1"></i>{{ $ledger->serial->serial_no }}
                            </span>
                        @else
                            <span class="text-muted small italic">Bulk/No Serial</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $ledger->change_qty > 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-2 py-1 mb-1">
                            {{ str_replace('_', ' ', $ledger->transaction_type) }}
                        </span><br>
                        <small class="text-muted italic">{{ $ledger->reason_code }}</small>
                    </td>
                    <td>
                        <code>{{ $ledger->reference_id }}</code>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold {{ $ledger->change_qty > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $ledger->change_qty > 0 ? '+' : '' }}{{ $ledger->change_qty }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bx bx-info-circle fs-40 mb-2"></i>
                        <p class="mb-0">No ledger entries found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($ledgers->hasPages())
    <div class="card-footer border-top-0 bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of {{ $ledgers->total() }} Results
            </div>
            <div>
                {{ $ledgers->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
@endif
