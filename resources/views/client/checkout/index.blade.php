@extends('client.structure.app', ['title' => 'Checkout', 'section' => 'Checkout'])

@section('content')
@php $gs = \App\HelperClass::generalSettings(); @endphp
<!-- checkout area start -->
<div class="checkout-area mtb-60px">
    <div class="container">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <div class="billing-info-wrap">
                        <h3>Shipping Details</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="billing-info mb-20px">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required />
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required />
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}" required />
                                    @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="billing-info mb-20px">
                                    <label>Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address" placeholder="House number and street name" value="{{ old('address', $user->address ?? '') }}" required />
                                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}" required />
                                    @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>State</label>
                                    <input type="text" name="state" value="{{ old('state', $user->state ?? '') }}" />
                                    @error('state') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Postcode / ZIP</label>
                                    <input type="text" name="zip" value="{{ old('zip', $user->zip ?? '') }}" />
                                    @error('zip') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Country</label>
                                    <input type="text" name="country" value="{{ old('country', $user->country ?? 'Bangladesh') }}" />
                                    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="additional-info-wrap">
                            <h3>Additional information</h3>
                            <div class="additional-info">
                                <label>Order notes</label>
                                <textarea placeholder="Notes about your order, e.g. special notes for delivery. " name="notes">{{ old('notes') }}</textarea>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="your-order-area">
                        <h3>Your order</h3>
                        <div class="your-order-wrap gray-bg-4">
                            <div class="your-order-product-info">
                                <div class="your-order-top">
                                    <ul>
                                        <li>Product</li>
                                        <li>Total</li>
                                    </ul>
                                </div>
                                <div class="your-order-middle">
                                    <ul>
                                        @foreach($cartItems as $item)
                                            <li class="mb-2">
                                                <span class="order-middle-left">
                                                    <strong>{{ $item->product_name }}</strong>
                                                    @if($item->variant_name)
                                                        <br><small class="text-muted">Variant: {{ $item->variant_details }}</small>
                                                    @endif
                                                    <br><small>Qty: {{ $item->quantity }}</small>
                                                </span>
                                                <span class="order-price">
                                                    @if($item->product_discount > 0)
                                                        <span class="text-decoration-line-through me-2" style="color: #999; font-size: 0.85em;">{{ $gs->currency ?? '$' }}{{ number_format($item->regular_price * $item->quantity, 2) }}</span>
                                                        {{ $gs->currency ?? '$' }}{{ number_format($item->subtotal, 2) }}
                                                    @else
                                                        {{ $gs->currency ?? '$' }}{{ number_format($item->subtotal, 2) }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="your-order-bottom">
                                    <ul>
                                        <li class="your-order-shipping">Shipping ({{ $selectedShippingMethod->name }})</li>
                                        <li>{{ $gs->currency ?? '$' }}{{ number_format($selectedShippingMethod->price, 2) }}</li>
                                    </ul>
                                </div>
                                <div class="your-order-bottom coupon-discount-row" style="{{ session('coupon') ? 'margin-top: 15px !important;' : 'display: none; margin-top: 15px !important;' }}">
                                    <ul>
                                        <li class="your-order-shipping">Discount ({{ session('coupon.code') }}) <a href="javascript:void(0)" id="remove-coupon-btn" class="text-danger small"><i class="fa fa-trash-o"></i></a></li>
                                        <li>-{{ $gs->currency ?? '$' }}<span id="discount-amount">{{ number_format(session('coupon.discount', 0), 2) }}</span></li>
                                    </ul>
                                </div>
                                <div class="your-order-total">
                                    <ul>
                                        <li class="order-total">Total</li>
                                        <li>{{ $gs->currency ?? '$' }}<span id="grand-total">{{ number_format($grandTotal - session('coupon.discount', 0), 2) }}</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="coupon-area mt-20 mb-40px p-3 bg-white border">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="mb-0 fw-bold">Have a coupon?</label>
                                    <button type="button" class="btn btn-sm px-3 rounded-pill shadow-sm btn-view-coupons" data-bs-toggle="modal" data-bs-target="#availableCouponsModal">
                                        <i class="fa fa-ticket me-1"></i> View Coupons
                                    </button>
                                </div>
                                <div class="input-group mt-2">
                                    <input type="text" id="coupon_code" class="form-control" placeholder="Enter coupon code" value="{{ session('coupon.code') }}">
                                    <button class="btn btn-dark" type="button" id="apply-coupon-btn">Apply</button>
                                </div>
                                <div id="coupon-message" class="mt-2 small" style="{{ session('coupon') ? '' : 'display: none;' }}">
                                    @if(session('coupon'))
                                        <span class="text-success">* Coupon Applied!</span>
                                    @endif
                                </div>
                            </div>
                            <div class="payment-method">
                                <div class="payment-accordion element-mrg">
                                    <div class="panel-group" id="accordion">
                                        <div class="panel payment-accordion">
                                            <div class="panel-heading" id="method-one">
                                                <h4 class="panel-title">
                                                    <input type="radio" name="payment_method" value="COD" checked id="payment_cod">
                                                    <label for="payment_cod">Cash on Delivery (COD)</label>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="panel payment-accordion">
                                            <div class="panel-heading" id="method-two">
                                                <h4 class="panel-title">
                                                    <input type="radio" name="payment_method" value="Online" id="payment_online">
                                                    <label for="payment_online">Pay Now (Online Payment)</label>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Place-order mt-25">
                            <button type="submit" class="btn btn-primary w-100 py-3" style="background-color: #333; border: none;">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- checkout area end -->

<!-- Available Coupons Modal -->
<div class="modal fade" id="availableCouponsModal" tabindex="-1" aria-labelledby="availableCouponsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold" id="availableCouponsModalLabel"><i class="fa fa-ticket me-2 text-primary"></i>Available Coupons</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #f8f9fa;">
                <div id="available-coupons-list">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-view-coupons {
        background: linear-gradient(135deg, #7AAACE, #9CC2E2);
        color: #fff;
        border: none;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .btn-view-coupons:hover {
        background: linear-gradient(135deg, #6b99ba, #7AAACE);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(122, 170, 206, 0.4) !important;
    }
    .coupon-card {
        background: #fff;
        border-radius: 12px;
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        overflow: hidden;
    }
    .coupon-card.eligible {
        border: 2px dashed #7AAACE;
        box-shadow: 0 4px 15px rgba(122, 170, 206, 0.1);
    }
    .coupon-card.eligible:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(122, 170, 206, 0.2);
    }
    .coupon-card.ineligible {
        border: 1px solid #ffcdd2;
        background: #fffafa;
    }
    .coupon-card .coupon-left {
        padding: 20px;
        background: transparent;
        flex-grow: 1;
    }
    .coupon-card .coupon-right {
        background: #f8f9fa;
        padding: 20px;
        border-left: 2px dotted #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 100px;
    }
    .coupon-card.eligible .coupon-right {
        background: rgba(122, 170, 206, 0.05);
        border-left-color: #7AAACE;
    }
    .coupon-card.ineligible .coupon-right {
        background: #ffebee;
        border-left-color: #ef9a9a;
    }
    .coupon-code-badge {
        display: inline-block;
        padding: 5px 12px;
        background: #f0f4f8;
        color: #253237;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 1px;
        border: 1px solid #d0dae2;
        margin-bottom: 8px;
    }
    .coupon-card.eligible .coupon-code-badge {
        background: #eef6ff;
        color: #7AAACE;
        border-color: #7AAACE;
    }
    .coupon-card.ineligible .coupon-code-badge {
        background: #ffebee;
        color: #c62828;
        border-color: #ef9a9a;
    }
    .ineligible-msg {
        font-size: 11px;
        padding: 6px 10px;
        background: #ffebee;
        color: #c62828;
        border-radius: 6px;
        display: inline-block;
        border: 1px solid #ffcdd2;
    }
</style>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const availableCouponsModal = document.getElementById('availableCouponsModal');
        availableCouponsModal.addEventListener('show.bs.modal', function () {
            $.ajax({
                url: "{{ route('checkout.available_coupons') }}",
                type: "GET",
                success: function(response) {
                    let html = '';
                    if (response.length > 0) {
                        response.forEach(item => {
                            const coupon = item.coupon;
                            const isEligible = item.is_eligible;
                            const reason = item.ineligible_reason;
                            
                            html += `
                                <div class="coupon-card mb-3 d-flex ${isEligible ? 'eligible' : 'ineligible'}">
                                    <div class="coupon-left">
                                        <div class="coupon-code-badge">${coupon.code}</div>
                                        <h6 class="fw-bold mb-1" style="color: #253237;">
                                            ${coupon.discount_type === 'percentage' ? coupon.discount_amount + '%' : '{{ $gs->currency ?? '$' }}' + parseFloat(coupon.discount_amount).toFixed(2)} OFF
                                        </h6>
                                        <p class="text-muted mb-2 small" style="font-size: 12px; line-height: 1.4;">
                                            Apply this code to get a discount on ${coupon.apply_for === 'total_product_price' ? 'your product subtotal' : 'shipping charges'}.
                                        </p>
                                        <div class="d-flex align-items-center gap-3 mt-2">
                                            <span class="small text-muted"><i class="fa fa-shopping-bag me-1"></i> Min: {{ $gs->currency ?? '$' }}${parseFloat(coupon.min_spend).toFixed(2)}</span>
                                            <span class="small text-muted"><i class="fa fa-calendar me-1"></i> Exp: ${new Date(coupon.expired_on).toLocaleDateString()}</span>
                                        </div>
                                        ${!isEligible ? `
                                            <div class="mt-2">
                                                <span class="ineligible-msg text-danger fw-bold">
                                                    <i class="fa fa-lock me-1"></i> ${reason}
                                                </span>
                                            </div>
                                        ` : ''}
                                    </div>
                                    <div class="coupon-right">
                                        <button type="button" class="btn ${isEligible ? 'btn-dark' : 'btn-secondary disabled'} btn-sm apply-modal-coupon px-3" 
                                                data-code="${coupon.code}" ${!isEligible ? 'disabled' : ''} 
                                                style="${isEligible ? 'background-color: #253237; border: none;' : ''}">
                                            ${isEligible ? 'APPLY' : 'LOCKED'}
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<div class="text-center py-5 text-muted"><i class="fa fa-ticket fa-3x mb-3 opacity-25"></i><p>No coupons available at the moment.</p></div>';
                    }
                    $('#available-coupons-list').html(html);
                },
                error: function() {
                    $('#available-coupons-list').html('<div class="text-center py-5 text-danger"><p>Failed to load coupons. Please try again.</p></div>');
                }
            });
        });

        $(document).on('click', '.apply-modal-coupon', function() {
            const code = $(this).data('code');
            $('#coupon_code').val(code);
            bootstrap.Modal.getInstance(availableCouponsModal).hide();
            $('#apply-coupon-btn').click();
        });

        $('#apply-coupon-btn').on('click', function() {
            const code = $('#coupon_code').val();
            if (!code) {
                toastr.error('Please enter a coupon code.');
                return;
            }

            $.ajax({
                url: "{{ route('checkout.apply_coupon') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    coupon_code: code
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('.coupon-discount-row').show();
                        $('.coupon-discount-row li:first-child').html(`Discount (${code}) <a href="javascript:void(0)" id="remove-coupon-btn" class="text-danger small"><i class="fa fa-trash-o"></i></a>`);
                        $('#discount-amount').text(response.discount);
                        $('#grand-total').text(response.grand_total);
                        
                        $('#coupon-message').html(`<span class="text-success">* ${response.message}</span>`).show();
                    } else {
                        toastr.error(response.message);
                        $('#coupon-message').html(`<span class="text-danger">* ${response.message}</span>`).show();
                    }
                }
            });
        });

        $(document).on('click', '#remove-coupon-btn', function() {
            $.ajax({
                url: "{{ route('checkout.remove_coupon') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        $('.coupon-discount-row').hide();
                        $('#coupon_code').val('');
                        $('#grand-total').text(response.grand_total);
                        $('#coupon-message').hide();
                    }
                }
            });
        });
    });
</script>
@endpush
