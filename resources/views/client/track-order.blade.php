@extends('client.structure.app')
@section('content')
    <style>
        .status-dot {
            transition: all 0.3s ease;
            z-index: 2;
            position: relative;
        }
        .track-progress {
            padding: 40px 0;
        }
        @media (max-width: 767px) {
            .cart-table-content table thead {
                display: none;
            }
            .cart-table-content table tbody tr {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                grid-template-areas: 
                    "name name"
                    "qty total";
                padding: 15px 10px;
                border-bottom: 1px solid #ebebeb;
            }
            .cart-table-content table tbody tr td {
                border: none;
                padding: 8px 5px;
                width: 100% !important;
                text-align: left !important;
            }
            .cart-table-content table tbody tr td.product-name {
                grid-area: name;
                font-weight: bold;
                padding-left: 0 !important;
            }
            .cart-table-content table tbody tr td.product-quantity {
                grid-area: qty;
                display: flex;
            }
            .cart-table-content table tbody tr td.product-quantity::before {
                content: "Qty: ";
                color: #777;
                margin-right: 5px;
            }
            .cart-table-content table tbody tr td.product-subtotal {
                grid-area: total;
                text-align: right !important;
                display: flex;
                justify-content: flex-end;
                padding-right: 0 !important;
            }
            .cart-table-content table tbody tr td.product-subtotal::before {
                content: "Total: ";
                color: #777;
                margin-right: 5px;
            }
        }
    </style>
    <!-- track order area start -->
    <div class="checkout-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-9">
                    <div class="checkout-wrapper">
                        <div class="panel-group">
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title text-center p-3" style="background-color: transparent !important; border: none;">
                                    <h3 class="panel-title">Track Your Order</h3>
                                    <p class="mb-0 mt-2 text-muted">Enter your Order ID to track your shipment status.</p>
                                </div>
                                <div class="panel-body">
                                    <div class="myaccount-info-wrapper p-4">
                                        <form action="{{ route('client.track_order') }}" method="GET" class="mb-5">
                                            <div class="row justify-content-center">
                                                <div class="col-md-8">
                                                    <div class="billing-info mb-0">
                                                        <label class="fw-bold text-dark">Order ID</label>
                                                        <div class="d-flex flex-column flex-sm-row gap-3 align-items-center">
                                                            <input type="text" name="order_id" value="{{ request('order_id') }}" placeholder="e.g. ORD-1234567890" required class="flex-grow-1 m-0">
                                                            <div>
                                                                <button type="submit" class="btn btn-primary px-5 text-white" style="background-color: #7AAACE; border-color: #7AAACE; height: 45px; font-weight: 700; text-transform: uppercase; font-size: 13px; border-radius: 0;">Track</button>
                                                            </div>
                                                        </div>
                                                        @error('order_id')
                                                            <div class="text-danger small mt-2 ps-1"><i class="fa fa-exclamation-circle me-1"></i> {{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        @if($order)
                                            <div class="order-tracking-result mt-5 pt-5 border-top">
                                                <div class="text-center mb-5">
                                                    <h4 class="fw-bold text-dark mb-3">Order #{{ $order->order_id }}</h4>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <span class="text-muted">Current Status:</span>
                                                        <span class="badge {{ match($order->order_status) {
                                                            'Pending' => 'bg-warning text-dark',
                                                            'Processing' => 'bg-info text-white',
                                                            'Out for Delivery' => 'bg-primary text-white',
                                                            'Delivered' => 'bg-success text-white',
                                                            'Cancelled', 'Rejected' => 'bg-danger text-white',
                                                            default => 'bg-secondary text-white'
                                                        } }} px-4 py-2 ms-3 fs-6 rounded-pill shadow-sm">{{ $order->order_status }}</span>
                                                    </div>
                                                </div>

                                                <!-- Status Progress Bar -->
                                                @php
                                                    $statuses = ['Pending', 'Processing', 'Out for Delivery', 'Delivered'];
                                                    $currentStatusIndex = array_search($order->order_status, $statuses);
                                                    $isCancelled = in_array($order->order_status, ['Cancelled', 'Rejected']);
                                                @endphp

                                                @if(!$isCancelled)
                                                    <div class="track-progress mb-5 px-lg-5 py-4">
                                                        <div class="position-relative">
                                                            <div class="progress" style="height: 8px; background-color: #f0f0f0; border-radius: 10px;">
                                                                <div class="progress-bar bg-success" role="progressbar" 
                                                                    style="width: {{ $currentStatusIndex !== false ? ($currentStatusIndex / (count($statuses) - 1)) * 100 : 0 }}%; transition: width 1s ease-in-out;" 
                                                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="d-flex justify-content-between position-absolute w-100 top-50 translate-middle-y">
                                                                @foreach($statuses as $index => $status)
                                                                    <div class="text-center position-relative" style="z-index: 2;">
                                                                        <div class="status-dot rounded-circle bg-white border border-4 {{ $currentStatusIndex >= $index ? 'border-success' : 'border-white shadow-sm' }}" 
                                                                             style="width: 24px; height: 24px; margin: 0 auto; transition: all 0.3s ease;">
                                                                            @if($currentStatusIndex >= $index)
                                                                                <div class="bg-success rounded-circle m-auto mt-1" style="width: 8px; height: 8px;"></div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="small mt-2 fw-bold {{ $currentStatusIndex >= $index ? 'text-success' : 'text-muted' }} d-none d-md-block">{{ $status }}</div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between mt-4 d-md-none px-2">
                                                            <span class="small fw-bold {{ $currentStatusIndex >= 0 ? 'text-success' : 'text-muted' }}">Ordered</span>
                                                            <span class="small fw-bold {{ $currentStatusIndex >= 3 ? 'text-success' : 'text-muted' }}">Delivered</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger text-center py-4 mb-5 border-0 shadow-sm rounded-0">
                                                        <i class="fa fa-times-circle fs-4 mb-2 d-block"></i>
                                                        This order has been <strong>{{ $order->order_status }}</strong> and cannot be tracked further.
                                                    </div>
                                                @endif

                                                <div class="row g-4 mt-4">
                                                    <div class="col-md-6">
                                                        <div class="p-4 border rounded bg-light-subtle h-100">
                                                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa fa-user me-2"></i>Customer Details</h6>
                                                            <ul class="list-unstyled mb-0 text-muted">
                                                                <li class="mb-2"><strong class="text-dark">Name:</strong> {{ $order->name }}</li>
                                                                <li class="mb-2"><strong class="text-dark">Phone:</strong> {{ $order->mobile }}</li>
                                                                <li class="mb-0"><strong class="text-dark">Address:</strong> {{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->zip }}</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-4 border rounded bg-light-subtle h-100">
                                                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa fa-credit-card me-2"></i>Order Summary</h6>
                                                            <ul class="list-unstyled mb-0 text-muted">
                                                                <li class="mb-2"><strong class="text-dark">Payment Method:</strong> {{ $order->payment_method }}</li>
                                                                <li class="mb-2"><strong class="text-dark">Date:</strong> {{ $order->created_at->format('M d, Y') }}</li>
                                                                <li class="mb-0"><strong class="text-dark">Grand Total:</strong> <span class="text-primary fw-bold">${{ number_format($order->total_amount, 2) }}</span></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="table-content table-responsive cart-table-content mt-5">
                                                    <table class="w-100">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-start ps-3">Items Ordered</th>
                                                                <th class="text-center">Qty</th>
                                                                <th class="text-end pe-3">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($order->orderItems as $item)
                                                                <tr>
                                                                    <td class="product-name text-start ps-3">
                                                                        <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                                                                        @if($item->variant_name)
                                                                            <span class="small text-muted ms-2">({{ $item->variant_name }})</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="product-quantity text-center text-dark">{{ $item->quantity }}</td>
                                                                    <td class="product-subtotal text-end pe-3">
                                                                        <span class="fw-bold text-dark">${{ number_format($item->total_price, 2) }}</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- track order area end -->

@endsection

@push('styles')
<style>
    .status-dot {
        transition: all 0.3s ease;
        z-index: 2;
        position: relative;
    }
    .track-progress {
        padding: 40px 0;
    }
</style>
@endpush
