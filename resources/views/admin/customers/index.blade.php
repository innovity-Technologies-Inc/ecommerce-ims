@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Customer List</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center g-2">
                            <div class="col-lg-4">
                                <div class="search-box">
                                    <input type="text" class="form-control" id="search-input" placeholder="Search customers..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <select class="form-select filter-select" id="status-select">
                                    <option value="">Status (All)</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-auto ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted text-nowrap">Sort By:</span>
                                    <select class="form-select" id="sort-select">
                                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                        <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A to Z</option>
                                        <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z to A</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="table-container">
                        @include('admin.customers.partials.table')
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

        function fetchCustomers() {
            const search = $('#search-input').val();
            const status = $('#status-select').val();
            const sort = $('#sort-select').val();
            const url = new URL(window.location.href);
            
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
            if (sort) url.searchParams.set('sort', sort); else url.searchParams.delete('sort');
            
            window.history.pushState({}, '', url);
            tableContainer.css('opacity', '0.5');

            $.ajax({
                url: url.href,
                type: 'GET',
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');
                },
                error: function() {
                    tableContainer.css('opacity', '1');
                }
            });
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchCustomers, 500);
        });

        $('#status-select, #sort-select').on('change', fetchCustomers);

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
            const url = `{{ route('admin.customers.toggle-status', ':id') }}`.replace(':id', id);

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
