<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
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
                                'Draft' => 'bg-secondary',
                                'Sent' => 'bg-info',
                                'Delivered' => 'bg-success',
                                default => 'bg-dark'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $po->status }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @can('po.view')
                            <a href="{{ route('admin.inventory.po.show', $po->id) }}" class="btn btn-soft-info btn-sm" title="View">
                                <i class="bx bx-show"></i>
                            </a>
                            @endcan

                            @can('po.edit')
                                @if($po->status !== 'Delivered')
                                <a href="{{ route('admin.inventory.po.edit', $po->id) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endif
                            @endcan

                            @can('po.delete')
                                @if($po->status !== 'Delivered')
                                <form action="{{ route('admin.inventory.po.destroy', $po->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-soft-danger btn-sm confirmDelete" title="Delete">
                                        <i class="bx bx-trash"></i>
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

<div class="mt-3 d-flex justify-content-between align-items-center">
    <div>
        <p class="text-muted mb-0">
            Showing {{ $pos->firstItem() ?? 0 }} to {{ $pos->lastItem() ?? 0 }} of {{ $pos->total() }} Results
        </p>
    </div>
    <div>
        {{ $pos->links() }}
    </div>
</div>
