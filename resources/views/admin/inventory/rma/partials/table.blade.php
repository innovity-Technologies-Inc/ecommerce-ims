<div class="table-responsive">
    <table class="table align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 50px;">#SL</th>
                <th>RMA Number</th>
                <th>Date</th>
                <th>Supplier</th>
                <th>PO Number</th>
                <th class="text-center">Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($rmas); @endphp
            @forelse($rmas as $rma)
                <tr>
                    <td class="text-center">{{ $sl++ }}</td>
                    <td><span class="fw-medium">{{ $rma->rma_number }}</span></td>
                    <td>{{ $rma->created_at->format('d M, Y') }}</td>
                    <td>{{ $rma->supplier->name }}</td>
                    <td>{{ $rma->purchaseOrder->po_number ?? 'N/A' }}</td>
                    <td class="text-center">
                        @switch($rma->status)
                            @case('pending')
                                <span class="badge bg-warning-subtle text-warning text-uppercase">Pending</span>
                                @break
                            @case('approved')
                                <span class="badge bg-info-subtle text-info text-uppercase">Approved</span>
                                @break
                            @case('shipped')
                                <span class="badge bg-primary-subtle text-primary text-uppercase">Shipped</span>
                                @break
                            @case('closed')
                                <span class="badge bg-success-subtle text-success text-uppercase">Closed</span>
                                @break
                        @endswitch
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.inventory.rma.show', $rma->id) }}" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-show"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-5">
                        <div class="text-muted">No RMAs found.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    {{ $rmas->appends(request()->query())->links() }}
</div>
