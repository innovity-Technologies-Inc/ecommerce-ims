@extends('admin.structure.app')
@section('content')

    <div class="container-xxl">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Supplier RMAs</h4>
            @can('supplier_rma.create')
            <a href="{{ route('admin.inventory.rma.create') }}" class="btn btn-primary btn-sm">Create RMA</a>
            @endcan
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-lg-3">
                        <div class="search-box">
                            <input type="text" class="form-control" id="search-input" placeholder="Search RMA #..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-auto ms-auto">
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select" id="supplier-select">
                                <option value="all">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <select class="form-select" id="status-select">
                                <option value="all">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
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
                @include('admin.inventory.rma.partials.table')
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchRmas() {
            const search = $('#search-input').val();
            const supplier = $('#supplier-select').val();
            const status = $('#status-select').val();
            const sort = $('#sort-select').val();
            const url = new URL(window.location.href);
            
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (supplier !== 'all') url.searchParams.set('supplier_id', supplier); else url.searchParams.delete('supplier_id');
            if (status !== 'all') url.searchParams.set('status', status); else url.searchParams.delete('status');
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
            searchTimer = setTimeout(fetchRmas, 500);
        });

        $('#supplier-select, #status-select, #sort-select').on('change', fetchRmas);

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
