@extends('admin.structure.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Purchase Orders</h4>
        @can('po.create')
        <a href="{{ route('admin.inventory.po.create') }}" class="btn btn-primary btn-sm">
            Add Purchase Order
        </a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row align-items-center g-2">
                <div class="col-lg-3">
                    <div class="search-box">
                        <input type="text" id="poSearch" class="form-control" placeholder="Search PO Number..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select id="statusFilter" class="form-select">
                        <option value="all">All Status</option>
                        <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Sent" {{ request('status') == 'Sent' ? 'selected' : '' }}>Sent</option>
                        <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select id="supplierFilter" class="form-select select2">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-auto ms-auto">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted text-nowrap">Sort By:</span>
                        <select id="sortFilter" class="form-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-auto">
                    <button type="button" id="resetFilters" class="btn btn-soft-secondary">Reset</button>
                </div>
            </div>
        </div>

        <div class="card-body p-0" id="table-container">
            @include('admin.inventory.po.partials.table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        const tableContainer = $('#table-container');
        let searchTimer;

        function fetchPOs() {
            const search = $('#poSearch').val();
            const status = $('#statusFilter').val();
            const supplier_id = $('#supplierFilter').val();
            const sort = $('#sortFilter').val();
            const url = new URL(window.location.href);

            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (status !== 'all') url.searchParams.set('status', status); else url.searchParams.delete('status');
            if (supplier_id) url.searchParams.set('supplier_id', supplier_id); else url.searchParams.delete('supplier_id');
            if (sort !== 'latest') url.searchParams.set('sort', sort); else url.searchParams.delete('sort');

            window.history.pushState({}, '', url);
            tableContainer.css('opacity', 0.5);

            $.ajax({
                url: url.href,
                type: 'GET',
                success: function(response) {
                    tableContainer.html(response).css('opacity', 1);
                }
            });
        }

        $('#poSearch').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchPOs, 500);
        });

        $('#statusFilter, #supplierFilter, #sortFilter').on('change', fetchPOs);

        $('#resetFilters').click(function() {
            $('#poSearch').val('');
            $('#statusFilter').val('all');
            $('#supplierFilter').val('').trigger('change');
            $('#sortFilter').val('latest');
            fetchPOs();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            tableContainer.css('opacity', 0.5);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    tableContainer.html(response).css('opacity', 1);
                    window.history.pushState({}, '', url);
                }
            });
        });
    });
</script>
@endsection
