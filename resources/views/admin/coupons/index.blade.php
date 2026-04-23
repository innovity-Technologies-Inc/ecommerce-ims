@extends('admin.structure.app')
@section('content')
@php $gs = \App\HelperClass::generalSettings(); @endphp

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Coupons</h4>
            @can('coupons.create')
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">Add Coupon</a>
            @endcan
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row g-2 align-items-end">
                    <!-- First Row: Search and Basic Filters -->
                    <div class="col-lg-4">
                        <label class="form-label small">Search</label>
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search coupon code..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small">Apply For</label>
                        <select class="form-select filter-select" id="apply-for-select">
                            <option value="">All</option>
                            <option value="total_product_price" {{ request('apply_for') == 'total_product_price' ? 'selected' : '' }}>Product Price</option>
                            <option value="shipping_cost" {{ request('apply_for') == 'shipping_cost' ? 'selected' : '' }}>Shipping Cost</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small">Status</label>
                        <select class="form-select filter-select" id="status-select">
                            <option value="">All</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small">Sort By</label>
                        <select class="form-select" id="sort-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A to Z</option>
                            <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z to A</option>
                        </select>
                    </div>
                    <div class="col-lg-2 text-end">
                        <button class="btn btn-outline-danger w-100" id="reset-filters" type="button">
                            Reset Filters
                        </button>
                    </div>

                    <!-- Second Row: Date Filters -->
                    <div class="col-lg-3">
                        <label class="form-label small">Active From</label>
                        <input type="date" class="form-control filter-select" id="active-on-from" value="{{ request('active_on_from') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small">Active To</label>
                        <input type="date" class="form-control filter-select" id="active-on-to" value="{{ request('active_on_to') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small">Expired From</label>
                        <input type="date" class="form-control filter-select" id="expired-on-from" value="{{ request('expired_on_from') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small">Expired To</label>
                        <input type="date" class="form-control filter-select" id="expired-on-to" value="{{ request('expired_on_to') }}">
                    </div>
                </div>
            </div>
            <div class="card-body p-0" id="table-container">
                @include('admin.coupons.partials.table')
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchCoupons() {
            const params = {
                search: $('#search-input').val(),
                apply_for: $('#apply-for-select').val(),
                status: $('#status-select').val(),
                sort: $('#sort-select').val(),
                active_on_from: $('#active-on-from').val(),
                active_on_to: $('#active-on-to').val(),
                expired_on_from: $('#expired-on-from').val(),
                expired_on_to: $('#expired-on-to').val(),
            };

            const url = new URL(window.location.href);
            Object.keys(params).forEach(key => {
                if (params[key]) {
                    url.searchParams.set(key, params[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });
            
            window.history.pushState({}, '', url);
            tableContainer.css('opacity', '0.5');

            $.ajax({
                url: url.href,
                type: 'GET',
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');
                }
            });
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchCoupons, 500);
        });

        $('.filter-select, #sort-select').on('change', fetchCoupons);

        $('#reset-filters').on('click', function() {
            $('#search-input').val('');
            $('.filter-select').val('');
            $('#sort-select').val('latest');
            fetchCoupons();
        });

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

        $(document).on('change', '.status-toggle', function() {
            const id = $(this).data('id');
            const url = `{{ route('admin.coupons.toggle-status', ':id') }}`.replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                    }
                }
            });
        });
    });
</script>
@endsection
