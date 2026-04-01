@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Warehouses</h4>
            @can('warehouse.create')
            <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary btn-sm">Add Warehouse</a>
            @endcan
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-lg-4">
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search warehouses..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-auto ms-auto">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-nowrap">Type:</span>
                            <select id="is_quarantine" class="form-select form-select-sm">
                                <option value="all" {{ request('is_quarantine') == 'all' ? 'selected' : '' }}>All</option>
                                <option value="0" {{ request('is_quarantine') == '0' ? 'selected' : '' }}>Normal</option>
                            </select>
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
                @include('admin.inventory.warehouses.partials.table')
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchWarehouses() {
            const search = $('#search-input').val();
            const type = $('#is_quarantine').val();
            const sort = $('#sort-select').val();
            const url = new URL(window.location.href);
            
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (type) url.searchParams.set('is_quarantine', type); else url.searchParams.delete('is_quarantine');
            if (sort) url.searchParams.set('sort', sort); else url.searchParams.delete('sort');
            
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
            searchTimer = setTimeout(fetchWarehouses, 500);
        });

        $('#is_quarantine, #sort-select').on('change', fetchWarehouses);

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
