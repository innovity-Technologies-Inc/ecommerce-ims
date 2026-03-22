@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Roles</h4>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">Add Role</a>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label">Search</label>
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search roles..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Sort By</label>
                        <select class="form-select filter-select" id="sort-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Name Z-A</option>
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset Filters</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" id="table-container">
                @include('admin.roles.partials.table')
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let searchTimer;
            const tableContainer = $('#table-container');

            function fetchRoles() {
                const search = $('#search-input').val();
                const sort = $('#sort-select').val();

                tableContainer.css('opacity', '0.5');

                $.ajax({
                    url: "{{ route('admin.roles.index') }}",
                    type: 'GET',
                    data: {
                        search: search,
                        sort: sort
                    },
                    success: function(response) {
                        tableContainer.html(response);
                        tableContainer.css('opacity', '1');

                        const url = new URL(window.location.href);
                        if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                        if (sort) url.searchParams.set('sort', sort); else url.searchParams.delete('sort');
                        window.history.pushState({}, '', url);
                    },
                    error: function() {
                        tableContainer.css('opacity', '1');
                        toastr.error('Failed to fetch roles');
                    }
                });
            }

            $('#search-input').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(fetchRoles, 500);
            });

            $('.filter-select').on('change', fetchRoles);

            $('#reset-filters').on('click', function() {
                $('#search-input').val('');
                $('#sort-select').val('latest');
                fetchRoles();
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
