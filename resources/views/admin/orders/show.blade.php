@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Order Details: {{ $order->order_id }}</h5>
                        <span class="text-muted">{{ $order->created_at->format('d M, Y h:i A') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead class="bg-light-subtle">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                    @if($item->variant_name)
                                                        <small class="text-muted">{{ $item->variant_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end mt-3">
                            <div class="col-lg-5 col-sm-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                        <tr>
                                            <th>Subtotal :</th>
                                            <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping :</th>
                                            <td class="text-end">${{ number_format($order->shipping_charge, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount :</th>
                                            <td class="text-end">${{ number_format($order->discount, 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <th class="fs-16">Total :</th>
                                            <td class="text-end fs-16 fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer & Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Customer Info</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $order->name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                                <p class="mb-1"><strong>Phone:</strong> {{ $order->mobile }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Shipping Address</h6>
                                <p class="mb-1">{{ $order->address }}</p>
                                <p class="mb-1">{{ $order->city }}, {{ $order->state }} {{ $order->zip }}</p>
                                <p class="mb-1">{{ $order->country }}</p>
                            </div>
                        </div>
                        @if($order->notes)
                            <div class="mt-3">
                                <h6>Order Notes</h6>
                                <div class="p-2 bg-light rounded">
                                    {{ $order->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label d-block text-muted small text-uppercase fw-bold">Current Status</label>
                            @php
                                $statusClass = match($order->order_status) {
                                    'Pending' => 'bg-warning',
                                    'Processing' => 'bg-info',
                                    'Out for Delivery' => 'bg-primary',
                                    'Delivered' => 'bg-success',
                                    'Cancelled' => 'bg-danger',
                                    'Rejected' => 'bg-secondary',
                                    default => 'bg-dark'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} text-white fs-14 px-3 py-2">{{ $order->order_status }}</span>
                        </div>

                        <hr>

                        @if(in_array($order->order_status, ['Delivered', 'Cancelled', 'Rejected']))
                            @php
                                $alertClass = $order->order_status === 'Delivered' ? 'alert-soft-success' : 'alert-soft-danger';
                                $iconClass = $order->order_status === 'Delivered' ? 'bx-check-circle' : 'bx-info-circle';
                            @endphp
                            <div class="alert {{ $alertClass }} border-0 mb-0" role="alert">
                                <i class="bx {{ $iconClass }} me-1"></i> This order is <strong>{{ $order->order_status }}</strong>. The status cannot be changed further.
                            </div>
                        @else
                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="order_status" class="form-label">Update Status</label>
                                    <select name="order_status" id="order_status" class="form-select">
                                        @foreach($statuses as $key => $value)
                                            <option value="{{ $key }}" {{ $order->order_status === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_notify" id="email_notify" value="1">
                                        <label class="form-check-label fw-medium" for="email_notify">Email Notify Customer</label>
                                    </div>
                                    <small class="text-muted">If checked, the customer will receive an email about this status update.</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2">Update Status</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status History</h5>
                    </div>
                    <div class="card-body">
                        @if($order->statusLogs->count() > 0)
                            <div class="timeline-wrapper">
                                @foreach($order->statusLogs as $log)
                                    <div class="d-flex mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            @php
                                                $logClass = match($log->status) {
                                                    'Pending' => 'bg-warning',
                                                    'Processing' => 'bg-info',
                                                    'Out for Delivery' => 'bg-primary',
                                                    'Delivered' => 'bg-success',
                                                    'Cancelled' => 'bg-danger',
                                                    'Rejected' => 'bg-secondary',
                                                    default => 'bg-dark'
                                                };
                                            @endphp
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle {{ $logClass }} shadow">
                                                    <i class="bx bx-check fs-12"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fs-14 fw-bold text-dark">{{ $log->status }}</h6>
                                            <p class="text-muted mb-0 small">
                                                <i class="bx bx-time-five me-1"></i> {{ $log->changed_at->format('d M, Y - h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">No history available.</p>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Invoice</h5>
                    </div>
                    <div class="card-body">
                        @if($order->invoice_no)
                            <div class="mb-3">
                                <label class="form-label d-block text-muted small text-uppercase fw-bold">Invoice Number</label>
                                <span class="fw-bold fs-16">{{ $order->invoice_no }}</span>
                                <br>
                                <small class="text-muted">Generated on: {{ $order->invoice_date->format('d M, Y') }}</small>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.orders.view-invoice', $order->id) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bx bx-show me-1"></i> View / Print Invoice
                                </a>
                                <form action="{{ route('admin.orders.regenerate-invoice', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-soft-secondary w-100">
                                        <i class="bx bx-refresh me-1"></i> Generate
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-center py-2">
                                <p class="text-muted mb-3">No invoice has been generated for this order yet.</p>
                                <form action="{{ route('admin.orders.generate-invoice', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-receipt me-1"></i> Generate Invoice
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment Info</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Method:</strong> {{ $order->payment_method }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge {{ $order->payment_status === 'Paid' ? 'bg-success' : 'bg-warning' }}">{{ $order->payment_status }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
