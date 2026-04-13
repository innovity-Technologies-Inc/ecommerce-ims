@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Orders</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="form-label">Search</label>
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search orders (ID, Name, Email)..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Order Status</label>
                        <select class="form-select filter-select" id="order-status-select">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ request('order_status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select filter-select" id="payment-method-select">
                            <option value="">All Methods</option>
                            @foreach($paymentMethods as $key => $value)
                                <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select filter-select" id="payment-status-select">
                            <option value="">All Status</option>
                            @foreach($paymentStatuses as $key => $value)
                                <option value="{{ $key }}" {{ request('payment_status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Sort By</label>
                        <select class="form-select filter-select" id="sort-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>ID A-Z</option>
                            <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>ID Z-A</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Date From</label>
                        <input type="date" class="form-control filter-select" id="date-from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Date To</label>
                        <input type="date" class="form-control filter-select" id="date-to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" id="table-container">
                @include('admin.orders.partials.table')
            </div>
        </div>
        </div>

        @endsection

        @section('scripts')
        <script>
        $(document).ready(function() {
            let searchTimer;
            const tableContainer = $('#table-container');

            function fetchOrders() {
                const search = $('#search-input').val();
                const orderStatus = $('#order-status-select').val();
                const paymentMethod = $('#payment-method-select').val();
                const paymentStatus = $('#payment-status-select').val();
                const dateFrom = $('#date-from').val();
                const dateTo = $('#date-to').val();
                const sort = $('#sort-select').val();

                // Add loading state
                tableContainer.css('opacity', '0.5');

                $.ajax({
                    url: "{{ route('admin.orders.index') }}",
                    type: 'GET',
                    data: {
                        search: search,
                        order_status: orderStatus,
                        payment_method: paymentMethod,
                        payment_status: paymentStatus,
                        date_from: dateFrom,
                        date_to: dateTo,
                        sort: sort
                    },
                    success: function(response) {
                        tableContainer.html(response);
                        tableContainer.css('opacity', '1');

                        // Update URL without refresh
                        const url = new URL(window.location.href);
                        if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                        if (orderStatus) url.searchParams.set('order_status', orderStatus); else url.searchParams.delete('order_status');
                        if (paymentMethod) url.searchParams.set('payment_method', paymentMethod); else url.searchParams.delete('payment_method');
                        if (paymentStatus) url.searchParams.set('payment_status', paymentStatus); else url.searchParams.delete('payment_status');
                        if (dateFrom) url.searchParams.set('date_from', dateFrom); else url.searchParams.delete('date_from');
                        if (dateTo) url.searchParams.set('date_to', dateTo); else url.searchParams.delete('date_to');
                        if (sort) url.searchParams.set('sort', sort); else url.searchParams.delete('sort');
                        window.history.pushState({}, '', url);
                    },
                    error: function() {
                        tableContainer.css('opacity', '1');
                        toastr.error('Failed to fetch orders');
                    }
                });
            }

            $('#search-input').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(fetchOrders, 500);
            });

            $('.filter-select').on('change', fetchOrders);

            $('#reset-filters').on('click', function() {
                $('#search-input').val('');
                $('.filter-select').val('');
                $('#sort-select').val('latest');
                fetchOrders();
            });

            // Handle pagination clicks
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');

                tableContainer.css('opacity', '0.5');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        tableContainer.html(response);
                        tableContainer.css('opacity', '1');
                        window.history.pushState({}, '', url);
                    }
                });
            });
        });
        </script>
        @endsection
