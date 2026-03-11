@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-24 fw-bold">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                            </div>
                            <h4 class="mb-1">{{ $customer->name }}</h4>
                            <p class="text-muted">{{ $customer->email }}</p>
                            <span class="badge {{ $customer->status ? 'bg-success' : 'bg-danger' }} fs-14 px-3 py-2">
                                {{ $customer->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <hr class="my-4">

                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                <tr>
                                    <th class="ps-0" scope="row">Mobile :</th>
                                    <td class="text-muted">{{ $customer->mobile ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Joined :</th>
                                    <td class="text-muted">{{ $customer->created_at->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Address :</th>
                                    <td class="text-muted">{{ $customer->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">City/State :</th>
                                    <td class="text-muted">{{ $customer->city ?? 'N/A' }}, {{ $customer->state ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Country :</th>
                                    <td class="text-muted">{{ $customer->country ?? 'N/A' }} {{ $customer->zip ? '('.$customer->zip.')' : '' }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Purchase History</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead class="bg-light-subtle">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($customer->orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold">{{ $order->order_id }}</a>
                                        </td>
                                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($order->order_status) {
                                                    'Pending' => 'bg-warning',
                                                    'Processing' => 'bg-info',
                                                    'Out for Delivery' => 'bg-primary',
                                                    'Delivered' => 'bg-success',
                                                    'Cancelled', 'Rejected' => 'bg-danger',
                                                    default => 'bg-dark'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} text-white">{{ $order->order_status }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-soft-primary btn-sm">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No orders found for this customer.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
