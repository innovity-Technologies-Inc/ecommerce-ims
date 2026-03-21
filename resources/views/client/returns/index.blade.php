@extends('client.structure.app')
@section('content')
    <div class="checkout-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-9">
                    <div class="checkout-wrapper">
                        <div class="panel-group">
                            <div class="panel panel-default single-my-account">
                                <div class="panel-heading my-account-title text-center p-3" style="background-color: transparent !important; border: none;">
                                    <h3 class="panel-title">Return Request</h3>
                                    <p class="mb-0 mt-2 text-muted">Enter your Order ID to request a return or track status.</p>
                                </div>
                                <div class="panel-body">
                                    <div class="myaccount-info-wrapper p-4">
                                        <div class="row justify-content-center mb-5">
                                            <div class="col-md-8">
                                                <div class="billing-info mb-4">
                                                    <label class="fw-bold text-dark">Order ID</label>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <input type="text" id="order_id_input" placeholder="e.g. ORD-1234567890" class="flex-grow-1 m-0">
                                                        <div class="d-flex gap-2 flex-shrink-0">
                                                            <button type="button" id="btn_fetch_order" class="btn btn-primary px-3 text-white" style="background-color: #7AAACE; border-color: #7AAACE; height: 45px; font-weight: 700; text-transform: uppercase; font-size: 11px; border-radius: 0; white-space: nowrap;">Get Details</button>
                                                            <button type="button" id="btn_track_return" class="btn btn-secondary px-3 text-white" style="height: 45px; font-weight: 700; text-transform: uppercase; font-size: 11px; border-radius: 0; white-space: nowrap;">Track Status</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="return_tracking_result" class="mb-5 d-none">
                                            <div class="alert alert-info border-0 rounded-0 shadow-sm p-4">
                                                <h5 class="fw-bold mb-3">Return Status for #<span id="track_order_id"></span></h5>
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><strong>Return ID:</strong> <span id="track_return_id"></span></p>
                                                        <p class="mb-1"><strong>Status:</strong> <span id="track_status" class="badge rounded-pill px-3 py-2"></span></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><strong>Reason:</strong> <span id="track_reason"></span></p>
                                                        <p class="mb-1 d-none" id="track_rejection_container"><strong>Rejection Reason:</strong> <span id="track_rejection_reason" class="text-danger"></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="order_details_container" class="d-none">
                                            <form id="return_request_form" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="order_id" id="hidden_order_id">
                                                <input type="hidden" name="order_id_pk" id="hidden_order_id_pk">
                                                
                                                <div id="order_items_html"></div>

                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="billing-info">
                                                            <label class="fw-bold">Why are you returning? <span class="text-danger">*</span></label>
                                                            <input type="text" name="reason" placeholder="Damage, wrong product, etc." required style="width: 100%; height: 50px;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="billing-info">
                                                            <label class="fw-bold mb-2">Upload Product Photo (Optional)</label>
                                                            <input type="file" name="image" class="form-control rounded-0">
                                                            <small class="text-muted">JPG, PNG, WEBP (Max 2MB)</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="checkout-account-toggle mt-5">
                                                    <button type="submit" class="btn-hover checkout-btn w-100" style="background-color: #263c97; color: white; border: none; padding: 15px; font-weight: bold; text-transform: uppercase;">Submit Return Request</button>
                                                </div>
                                            </form>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const urlOrderId = urlParams.get('order_id');
        if (urlOrderId) {
            $('#order_id_input').val(urlOrderId);
            setTimeout(() => {
                $('#btn_fetch_order').click();
            }, 500);
        }

        $('#btn_fetch_order').on('click', function() {
            const orderId = $('#order_id_input').val();
            if (!orderId) {
                toastr.error('Please enter Order ID');
                return;
            }

            $.ajax({
                url: "{{ route('client.returns.order_details') }}",
                type: "GET",
                data: { order_id: orderId },
                beforeSend: function() {
                    $('#btn_fetch_order').prop('disabled', true).text('Loading...');
                },
                success: function(response) {
                    $('#order_details_container').removeClass('d-none');
                    $('#return_tracking_result').addClass('d-none');
                    $('#order_items_html').html(response.html);
                    $('#hidden_order_id').val(response.order.order_id);
                    $('#hidden_order_id_pk').val(response.order.id);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Error fetching order details');
                    $('#order_details_container').addClass('d-none');
                },
                complete: function() {
                    $('#btn_fetch_order').prop('disabled', false).text('Get Details');
                }
            });
        });

        $('#btn_track_return').on('click', function() {
            const orderId = $('#order_id_input').val();
            if (!orderId) {
                toastr.error('Please enter Order ID');
                return;
            }

            $.ajax({
                url: "{{ route('client.returns.track') }}",
                type: "GET",
                data: { order_id: orderId },
                beforeSend: function() {
                    $('#btn_track_return').prop('disabled', true).text('Loading...');
                },
                success: function(response) {
                    $('#return_tracking_result').removeClass('d-none');
                    $('#order_details_container').addClass('d-none');
                    
                    $('#track_order_id').text(orderId);
                    $('#track_return_id').text(response.return_id);
                    $('#track_reason').text(response.reason);
                    
                    const status = response.status;
                    let badgeClass = 'bg-secondary';
                    if (status === 'Pending') badgeClass = 'bg-warning text-dark';
                    else if (status === 'Approved') badgeClass = 'bg-info text-white';
                    else if (status === 'Received') badgeClass = 'bg-success text-white';
                    else if (status === 'Rejected') badgeClass = 'bg-danger text-white';
                    
                    $('#track_status').text(status).removeClass().addClass('badge rounded-pill px-3 py-2 ' + badgeClass);
                    
                    if (status === 'Rejected' && response.rejection_reason) {
                        $('#track_rejection_container').removeClass('d-none');
                        $('#track_rejection_reason').text(response.rejection_reason);
                    } else {
                        $('#track_rejection_container').addClass('d-none');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Error tracking return');
                    $('#return_tracking_result').addClass('d-none');
                },
                complete: function() {
                    $('#btn_track_return').prop('disabled', false).text('Track Status');
                }
            });
        });

        $('#return_request_form').on('submit', function(e) {
            e.preventDefault();
            
            // Check if at least one item has quantity > 0
            let hasItem = false;
            $('.item-qty').each(function() {
                if ($(this).val() > 0) hasItem = true;
            });

            if (!hasItem) {
                toastr.error('Please select at least one item to return');
                return;
            }

            const formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('client.returns.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('.checkout-btn').prop('disabled', true).text('Submitting...');
                },
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: function() {
                    $('.checkout-btn').prop('disabled', false).text('Submit Return Request');
                }
            });
        });
    });
</script>
@endpush
