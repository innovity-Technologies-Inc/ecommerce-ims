@extends('admin.structure.app')

@section('title', 'All Notifications')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Notifications</h4>
                <div class="page-title-right">
                    <form action="{{ route('admin.notifications.mark_all_read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-soft-primary btn-sm">
                            <i class="bx bx-check-double me-1"></i> Mark All as Read
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Search</label>
                                <input type="text" name="search" id="search-input" class="form-control" placeholder="Title or message..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Type</label>
                                <select name="type" id="type-select" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>Order</option>
                                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                                    <option value="low_stock" {{ request('type') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="message" {{ request('type') == 'message' ? 'selected' : '' }}>Message</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Date From</label>
                                <input type="date" name="date_from" id="date-from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Date To</label>
                                <input type="date" name="date_to" id="date-to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="button" id="reset-btn" class="btn btn-soft-secondary w-100">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0" id="table-container">
                    @include('admin.notifications.partials.table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchNotifications(pageUrl = null) {
            const search = $('#search-input').val();
            const type = $('#type-select').val();
            const dateFrom = $('#date-from').val();
            const dateTo = $('#date-to').val();
            
            const url = new URL(pageUrl || window.location.href);
            
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (type) url.searchParams.set('type', type); else url.searchParams.delete('type');
            if (dateFrom) url.searchParams.set('date_from', dateFrom); else url.searchParams.delete('date_from');
            if (dateTo) url.searchParams.set('date_to', dateTo); else url.searchParams.delete('date_to');
            
            if (!pageUrl) url.searchParams.delete('page'); // Reset to page 1 on filter change

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
            searchTimer = setTimeout(fetchNotifications, 500);
        });

        $('#type-select, #date-from, #date-to').on('change', function() {
            fetchNotifications();
        });

        $('#reset-btn').on('click', function() {
            $('#filter-form')[0].reset();
            fetchNotifications("{{ route('admin.notifications.index') }}");
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchNotifications($(this).attr('href'));
        });
    });
</script>
@endsection
