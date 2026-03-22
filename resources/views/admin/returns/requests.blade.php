@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Return Requests</h4>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="search-input" placeholder="Search by Return ID or Order ID..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Status</label>
                    <select class="form-select filter-select" id="status-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset Filters</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0" id="table-container">
            @include('admin.returns.partials.requests_table')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchRequests() {
            const search = $('#search-input').val();
            const status = $('#status-select').val();

            tableContainer.css('opacity', '0.5');

            $.ajax({
                url: "{{ route('admin.returns.requests') }}",
                type: 'GET',
                data: {
                    search: search,
                    status: status
                },
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');

                    const url = new URL(window.location.href);
                    if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                    if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
                    window.history.pushState({}, '', url);
                },
                error: function() {
                    tableContainer.css('opacity', '1');
                    toastr.error('Failed to fetch return requests');
                }
            });
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchRequests, 500);
        });

        $('.filter-select').on('change', fetchRequests);

        $('#reset-filters').on('click', function() {
            $('#search-input').val('');
            $('#status-select').val('');
            fetchRequests();
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
    });
</script>
@endsection
