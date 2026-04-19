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
            <div class="card-body p-0 position-relative" id="table-container">
                <div id="loadingSpinner" class="position-absolute top-50 start-50 translate-middle d-none" style="z-index: 10;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="tableContent">
                    @include('admin.inventory.rma.partials.table')
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
        const tableContent = $('#tableContent');
        const loadingSpinner = $('#loadingSpinner');

        function fetchRmas(url = null) {
            const search = $('#search-input').val();
            const supplier = $('#supplier-select').val();
            const status = $('#status-select').val();
            const sort = $('#sort-select').val();
            
            let finalUrl = url ? new URL(url) : new URL(window.location.href);
            
            if (!url) {
                if (search) finalUrl.searchParams.set('search', search); else finalUrl.searchParams.delete('search');
                if (supplier !== 'all') finalUrl.searchParams.set('supplier_id', supplier); else finalUrl.searchParams.delete('supplier_id');
                if (status !== 'all') finalUrl.searchParams.set('status', status); else finalUrl.searchParams.delete('status');
                if (sort) finalUrl.searchParams.set('sort', sort); else finalUrl.searchParams.delete('sort');
            }
            
            // Maintain height
            tableContainer.css('min-height', tableContainer.height() + 'px');
            tableContent.css('opacity', '0.5');
            loadingSpinner.removeClass('d-none');

            $.ajax({
                url: finalUrl.href,
                type: 'GET',
                success: function(response) {
                    tableContent.html(response);
                    tableContent.css('opacity', '1');
                    loadingSpinner.addClass('d-none');
                    tableContainer.css('min-height', '');
                    window.history.pushState({}, '', finalUrl.href);
                },
                error: function() {
                    tableContent.css('opacity', '1');
                    loadingSpinner.addClass('d-none');
                    tableContainer.css('min-height', '');
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
            fetchRmas(url);
        });
    });
</script>
@endsection
