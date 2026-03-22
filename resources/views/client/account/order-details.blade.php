@extends('client.structure.app')
@section('content')
    <style>
        @media (max-width: 767px) {
            .cart-table-content table thead {
                display: none;
            }
            .cart-table-content table tbody tr {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                grid-template-areas: 
                    "name name"
                    "price qty"
                    "total total";
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
                font-size: 15px;
            }
            .cart-table-content table tbody tr td.product-price-cart {
                grid-area: price;
                display: flex;
            }
            .cart-table-content table tbody tr td.product-price-cart::before {
                content: "Price: ";
                color: #777;
                margin-right: 5px;
            }
            .cart-table-content table tbody tr td.product-quantity {
                grid-area: qty;
                text-align: right !important;
                display: flex;
                justify-content: flex-end;
            }
            .cart-table-content table tbody tr td.product-quantity::before {
                content: "Qty: ";
                color: #777;
                margin-right: 5px;
            }
            .cart-table-content table tbody tr td.product-subtotal {
                grid-area: total;
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-top: 1px dashed #eee;
                margin-top: 5px;
                padding-right: 0 !important;
            }
            .cart-table-content table tbody tr td.product-subtotal::before {
                content: "Subtotal";
                color: #777;
                font-weight: normal;
            }
        }
    </style>
    <!-- order details area start -->
    <div class="checkout-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-12">
                    <div class="checkout-wrapper">
                        <div class="panel-group">
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title d-flex flex-column flex-sm-row justify-content-between align-items-sm-center p-3 gap-3" style="background-color: transparent !important; border: none;">
                                    <div class="d-flex align-items-center">
                                        <h3 class="panel-title mb-0">Order Details: {{ $order->order_id }}</h3>
                                        <span class="badge {{ match($order->order_status) {
                                            'Pending' => 'bg-warning text-dark',
                                            'Processing' => 'bg-info text-white',
                                            'Out for Delivery' => 'bg-primary text-white',
                                            'Delivered' => 'bg-success text-white',
                                            'Cancelled', 'Rejected' => 'bg-danger text-white',
                                            default => 'bg-secondary text-white'
                                        } }} px-3 py-2 rounded-pill ms-3">
                                            {{ $order->order_status }}
                                        </span>
                                    </div>
                                    <div class="m-0 p-0 border-0">
                                        <a href="{{ route('client.track_order', ['order_id' => $order->order_id]) }}" class="btn btn-primary px-4 py-2 text-white shadow-sm" style="background-color: #7AAACE; border-color: #7AAACE; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; border-radius: 0;">
                                            <i class="fa fa-map-marker-alt me-2"></i> Track Order
                                        </a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="myaccount-info-wrapper p-3">
                                        <div class="row mb-5 mt-2">
                                            <div class="col-md-6 mb-res-sm-30px">
                                                <div class="p-3 border rounded h-100 bg-light-subtle">
                                                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">Order Information</h5>
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="mb-2"><strong>Order Date:</strong> <span class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</span></li>
                                                        <li class="mb-2"><strong>Payment Method:</strong> <span class="text-muted">{{ $order->payment_method }}</span></li>
                                                        <li class="mb-2"><strong>Payment Status:</strong> <span class="badge bg-secondary px-2 ms-1">{{ $order->payment_status }}</span></li>
                                                        <li class="mb-0"><strong>Shipping Method:</strong> <span class="text-muted">{{ $order->shipping_method_name }}</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded h-100 bg-light-subtle">
                                                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">Shipping Address</h5>
                                                    <address class="mb-0 text-muted" style="line-height: 1.6;">
                                                        <span class="text-dark fw-bold d-block mb-1">{{ $order->name }}</span>
                                                        {{ $order->email }}<br>
                                                        {{ $order->mobile }}<br>
                                                        {{ $order->address }}<br>
                                                        {{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>
                                                        {{ $order->country }}
                                                    </address>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-content table-responsive cart-table-content">
                                            <table class="w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-start ps-3">Product</th>
                                                        <th>Price</th>
                                                        <th>Qty</th>
                                                        <th class="text-end pe-3">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->orderItems as $item)
                                                        <tr>
                                                            <td class="product-name text-start ps-3">
                                                                <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                                                                @if($item->variant_name)
                                                                    <div class="small text-muted mt-1">{{ $item->variant_name }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="product-price-cart">
                                                                <span class="amount">${{ number_format($item->unit_price, 2) }}</span>
                                                            </td>
                                                            <td class="product-quantity text-dark">
                                                                {{ $item->quantity }}
                                                            </td>
                                                            <td class="product-subtotal text-end pe-3">
                                                                <span class="fw-bold text-dark">${{ number_format($item->total_price, 2) }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="row justify-content-end mt-4">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="grand-totall mt-0 w-100 border p-3 rounded bg-light-subtle">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Subtotal</span>
                                                        <span class="fw-bold text-dark">${{ number_format($order->subtotal, 2) }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Shipping</span>
                                                        <span class="fw-bold text-dark">${{ number_format($order->shipping_charge, 2) }}</span>
                                                    </div>
                                                    @if($order->discount > 0)
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Discount</span>
                                                            <span class="fw-bold text-danger">-${{ number_format($order->discount, 2) }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                                        <h4 class="mb-0 text-primary">Grand Total</h4>
                                                        <h4 class="mb-0 text-primary fw-bold">${{ number_format($order->total_amount, 2) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($order->notes)
                                            <div class="mt-5 p-3 bg-warning-subtle rounded border border-warning-subtle">
                                                <h6 class="fw-bold mb-2 text-warning-emphasis">Order Notes:</h6>
                                                <p class="mb-0 text-muted italic">{{ $order->notes }}</p>
                                            </div>
                                        @endif

                                        <div class="border-top pt-4 mt-5 d-flex flex-column flex-sm-row justify-content-between gap-3 align-items-center">
                                            <div class="d-flex gap-2 flex-nowrap overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                                                <style>
                                                    .d-flex.gap-2::-webkit-scrollbar { display: none; }
                                                </style>
                                                <a href="{{ route('user.orders') }}" class="btn btn-dark px-3 py-2 text-white flex-shrink-0" style="background-color: #333; border-color: #333; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; border-radius: 0; white-space: nowrap;">
                                                    <i class="fa fa-arrow-left me-1"></i> Back
                                                </a>
                                                <a href="{{ route('client.track_order', ['order_id' => $order->order_id]) }}" class="btn btn-primary px-3 py-2 text-white flex-shrink-0" style="background-color: #7AAACE; border-color: #7AAACE; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; border-radius: 0; white-space: nowrap;">
                                                    <i class="fa fa-map-marker-alt me-1"></i> Track
                                                </a>
                                                @if($order->order_status === 'Delivered')
                                                    <a href="{{ route('client.returns.index', ['order_id' => $order->order_id]) }}" class="btn btn-danger px-3 py-2 text-white flex-shrink-0" style="background-color: #d9534f; border-color: #d43f3a; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; border-radius: 0; white-space: nowrap;">
                                                        <i class="fa fa-undo me-1"></i> Return
                                                    </a>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('user.view_invoice', $order->order_id) }}" target="_blank" class="btn btn-dark px-5 py-2 text-white" style="background-color: #333; border-color: #333; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; height: 45px; border-radius: 0; line-height: 28px; display: inline-block;">
                                                    <i class="fa fa-download me-2"></i> Download Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- order details area end -->

@endsection
