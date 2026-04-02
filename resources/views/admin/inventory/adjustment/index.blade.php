@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Stock Adjustments</h4>
            @can('stock_adjustment.create')
            <a href="{{ route('admin.inventory.adjustment.create') }}" class="btn btn-primary btn-sm">New Adjustment</a>
            @endcan
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-lg-3">
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search Adjustment #..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-auto ms-auto">
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select" id="warehouse-select">
                                <option value="all">All Warehouses</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <select class="form-select" id="sort-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" id="table-container">
                @include('admin.inventory.adjustment.partials.table')
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchAdjustments() {
            const search = $('#search-input').val();
            const warehouse = $('#warehouse-select').val();
            const sort = $('#sort-select').val();
            const url = new URL(window.location.href);
            
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (warehouse !== 'all') url.searchParams.set('warehouse_id', warehouse); else url.searchParams.delete('warehouse_id');
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
            searchTimer = setTimeout(fetchAdjustments, 500);
        });

        $('#warehouse-select, #sort-select').on('change', fetchAdjustments);

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
