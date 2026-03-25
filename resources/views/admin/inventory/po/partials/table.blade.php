<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pos as $po)
                <tr>
                    <td>{{ ($pos->currentPage() - 1) * $pos->perPage() + $loop->iteration }}</td>
                    <td><strong>{{ $po->po_number }}</strong></td>
                    <td>{{ $po->supplier->name }}</td>
                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                    <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : '-' }}</td>
                    <td>{{ number_format($po->total_amount, 2) }}</td>
                    <td>
                        @php
                            $badgeClass = match($po->status) {
                                'Draft' => 'badge-soft-secondary',
                                'Sent' => 'badge-soft-info',
                                'Delivered' => 'badge-soft-success',
                                default => 'badge-soft-dark'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $po->status }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @can('po.view')
                            <a href="{{ route('admin.inventory.po.show', $po->id) }}" class="btn btn-soft-info btn-sm" title="View">
                                <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                            </a>
                            @endcan

                            @can('po.edit')
                                @if($po->status !== 'Delivered')
                                <a href="{{ route('admin.inventory.po.edit', $po->id) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                </a>
                                @endif
                            @endcan

                            @can('po.delete')
                                @if($po->status !== 'Delivered')
                                <form action="{{ route('admin.inventory.po.destroy', $po->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" title="Delete">
                                        <iconify-icon icon="solar:trash-bin-trash-broken" class="align-middle fs-18"></iconify-icon>
                                    </button>
                                </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No Purchase Orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            Showing <span class="fw-semibold">{{ $pos->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $pos->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $pos->total() }}</span> Results
        </div>
        <div>
            {{ $pos->appends(request()->all())->links() }}
        </div>
    </div>
</div>
