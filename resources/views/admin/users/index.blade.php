@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Admins</h4>
            <a href="{{ route('admin.create') }}" class="btn btn-primary btn-sm">Add</a>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-lg-3">
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search admins..." value="{{ request('search') }}">
                        </div>
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
                @include('admin.users.partials.table')
            </div>
        </div> <!-- end card -->

    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchAdmins() {
            const search = $('#search-input').val();
            const sort = $('#sort-select').val();
            const url = new URL(window.location.href);
            
            url.searchParams.set('search', search);
            url.searchParams.set('sort', sort);
            
            // Update URL without refresh
            window.history.pushState({}, '', url);

            // Add loading state
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
                    toastr.error('Failed to fetch admins');
                }
            });
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchAdmins, 500);
        });

        $('#sort-select').on('change', fetchAdmins);

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

