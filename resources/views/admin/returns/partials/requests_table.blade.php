<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-3">#</th>
                <th>Return ID</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Date</th>
                <th class="text-end pe-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($requests); @endphp
            @forelse($requests as $request)
                <tr>
                    <td class="ps-3">{{ $sl++ }}</td>
                    <td><span class="fw-bold">{{ $request->return_id }}</span></td>
                    <td>{{ $request->order->order_id }}</td>
                    <td>{{ $request->order->name }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 150px;">{{ $request->reason }}</span></td>
                    <td>
                        <span class="badge {{ match($request->status) {
                            'pending' => 'bg-warning-subtle text-warning',
                            'approved' => 'bg-info-subtle text-info',
                            'received' => 'bg-success-subtle text-success',
                            'rejected' => 'bg-danger-subtle text-danger',
                            default => 'bg-secondary-subtle text-secondary'
                        } }} px-2 py-1">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.returns.show_request', $request->id) }}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" title="View Details">
                            <i class="bx bx-show fs-16"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No return requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($requests->hasPages())
    <div class="card-footer border-top-0">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted">
                Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} Results
            </div>
            {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
