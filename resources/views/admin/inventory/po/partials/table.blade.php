<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
            <tr>
                <th style="width: 50px;">#</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Target Warehouse</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($pos); @endphp
            @forelse($pos as $po)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td><strong>{{ $po->po_number }}</strong></td>
                    <td>{{ $po->supplier->name }}</td>
                    <td>
                        <span class="badge badge-soft-primary">{{ $po->warehouse->name ?? 'N/A' }}</span>
                    </td>
                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                    <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : '-' }}</td>
                    @php $gs = \App\HelperClass::generalSettings(); @endphp
                    <td>{{ $gs->currency ?? '$' }}{{ number_format($po->total_amount, 2) }}</td>
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

                            @if($po->status === 'Sent')
                                @can('po.edit')
                                <a href="{{ route('admin.inventory.po.receive', $po->id) }}" class="btn btn-soft-success btn-sm" title="Receive PO">
                                    <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="align-middle fs-18"></iconify-icon>
                                </a>
                                @endcan
                            @endif

                            @can('po.edit')
                                @if($po->status === 'Draft')
                                <a href="{{ route('admin.inventory.po.edit', $po->id) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                </a>
                                @endif
                            @endcan

                            @can('po.delete')
                                @if($po->status === 'Draft')
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
                    <td colspan="9" class="text-center p-4">
                        <iconify-icon icon="solar:box-minimalistic-broken" class="fs-48 text-muted mb-2"></iconify-icon>
                        <p class="text-muted">No Purchase Orders found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $pos->appends(request()->all())->links() }}
</div>
