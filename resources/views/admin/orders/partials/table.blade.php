<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-subtle">
        <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $sl = \App\HelperClass::indexNumberSerialization($orders);
        @endphp
        @foreach ($orders as $data)
        <tr>
            <td>{{$sl++}}</td>
            <td><span class="fw-bold">{{ $data->order_id }}</span></td>
            <td>
                <div class="d-flex flex-column">
                    <span>{{ $data->name }}</span>
                    <small class="text-muted">{{ $data->email }}</small>
                </div>
            </td>
            <td>{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($data->total_amount, 2) }}</td>
            <td>
                <span class="badge {{ $data->payment_method === 'COD' ? 'bg-info' : 'bg-primary' }} text-white">
                    {{ $data->payment_method }}
                </span>
                <br>
                <small class="text-muted">{{ $data->payment_status }}</small>
            </td>
            <td>
                @php
                    $statusClass = match($data->order_status) {
                        'Pending' => 'bg-warning',
                        'Processing' => 'bg-info',
                        'Out for Delivery' => 'bg-primary',
                        'Delivered' => 'bg-success',
                        'Cancelled' => 'bg-danger',
                        'Rejected' => 'bg-secondary',
                        default => 'bg-dark'
                    };
                @endphp
                <span class="badge {{ $statusClass }} text-white">{{ $data->order_status }}</span>
            </td>
            <td>{{ $data->created_at->format('d M, Y') }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.show', $data->id) }}" class="btn btn-soft-primary btn-sm">
                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        @if($orders->isEmpty())
            <tr>
                <td colspan="8" class="text-center">No orders found.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="card-footer border-top">
    <div class="d-flex align-items-center justify-content-between">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $orders->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $orders->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $orders->total() }}</span> Results
        </div>
        <div>
            {{ $orders->appends(request()->all())->links() }}
        </div>
    </div>
</div>
