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
                    "id date"
                    "status status"
                    "total total"
                    "action action";
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
                grid-area: id;
                font-weight: bold;
                padding-left: 0 !important;
            }
            .cart-table-content table tbody tr td.product-name::before {
                content: "Order: ";
                color: #777;
                font-weight: normal;
            }
            .cart-table-content table tbody tr td.product-price-cart {
                grid-area: date;
                text-align: right !important;
            }
            .cart-table-content table tbody tr td.product-subtotal:nth-child(3) {
                grid-area: status;
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-top: 1px dashed #eee;
                margin-top: 5px;
            }
            .cart-table-content table tbody tr td.product-subtotal:nth-child(3)::before {
                content: "Status";
                color: #777;
            }
            .cart-table-content table tbody tr td.product-subtotal:nth-child(4) {
                grid-area: total;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .cart-table-content table tbody tr td.product-subtotal:nth-child(4)::before {
                content: "Total Amount";
                color: #777;
            }
            .cart-table-content table tbody tr td.product-wishlist-cart {
                grid-area: action;
                padding-right: 0 !important;
                padding-top: 15px;
            }
            .cart-table-content table tbody tr td.product-wishlist-cart a {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
    <!-- account area start -->
    <div class="checkout-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-12">
                    <div class="checkout-wrapper">
                        <div class="panel-group">
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title p-3" style="background-color: transparent !important; border: none;">
                                    <h3 class="panel-title">Order History</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="myaccount-info-wrapper">
                                        <div class="table-content table-responsive cart-table-content">
                                            <table class="w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-start ps-3">Order ID</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Total</th>
                                                        <th class="text-end pe-3">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($orders as $order)
                                                        <tr>
                                                            <td class="product-name text-start ps-3">
                                                                <a href="{{ route('user.order_details', $order->order_id) }}" class="fw-bold">
                                                                    {{ $order->order_id }}
                                                                </a>
                                                            </td>
                                                            <td class="product-price-cart">
                                                                <span class="amount">{{ $order->created_at->format('M d, Y') }}</span>
                                                            </td>
                                                            <td class="product-subtotal">
                                                                <span class="badge {{ match($order->order_status) {
                                                                    'Pending' => 'bg-warning text-dark',
                                                                    'Processing' => 'bg-info text-white',
                                                                    'Out for Delivery' => 'bg-primary text-white',
                                                                    'Delivered' => 'bg-success text-white',
                                                                    'Cancelled', 'Rejected' => 'bg-danger text-white',
                                                                    default => 'bg-secondary text-white'
                                                                } }} px-3 py-2 rounded-pill">
                                                                    {{ $order->order_status }}
                                                                </span>
                                                            </td>
                                                            <td class="product-subtotal">
                                                                <span class="fw-bold text-dark">${{ number_format($order->total_amount, 2) }}</span>
                                                            </td>
                                                            <td class="product-wishlist-cart text-end pe-3">
                                                                <div class="d-flex justify-content-end gap-2">
                                                                    <a href="{{ route('client.track_order', ['order_id' => $order->order_id]) }}" class="btn btn-primary btn-sm px-3 text-white" style="background-color: #7AAACE; border-color: #7AAACE; text-transform: uppercase; font-weight: 700; font-size: 11px;">Track</a>
                                                                    <a href="{{ route('user.order_details', $order->order_id) }}" class="btn btn-dark btn-sm px-3 text-white" style="background-color: #333; border-color: #333; text-transform: uppercase; font-weight: 700; font-size: 11px;">Details</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-5">
                                                                <h4 class="mb-3">You haven't placed any orders yet.</h4>
                                                                <div class="mt-4">
                                                                    <a href="{{ route('client.products.index') }}" class="btn btn-primary px-5 py-3 text-white" style="background-color: #7AAACE; border-color: #7AAACE; font-weight: 700; text-transform: uppercase; border-radius: 0;">Start Shopping</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-4 pro-pagination-style text-center">
                                            <div class="text-muted mb-2">
                                                Showing <span class="fw-semibold">{{ $orders->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $orders->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $orders->total() }}</span> Results
                                            </div>
                                            {{ $orders->links() }}
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
    <!-- account area end -->

@endsection
