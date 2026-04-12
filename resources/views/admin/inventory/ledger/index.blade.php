@extends('admin.structure.app')

@section('title', 'Stock Ledger Report')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Stock Ledger Report</h4>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom-0 pb-3">
            <form id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <div class="search-box">
                            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Product, Batch, Serial, Ref...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Warehouse</label>
                        <select name="warehouse_id" id="warehouseFilter" class="form-select select2">
                            <option value="all">All Warehouses</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Type</label>
                        <select name="transaction_type" id="typeFilter" class="form-select">
                            <option value="all">All Types</option>
                            <option value="PO_RECEIPT">PO Receipt</option>
                            <option value="SALE">Sale</option>
                            <option value="DAMAGED">Damage</option>
                            <option value="STOCK_ADJUSTMENT">Adjustment</option>
                            <option value="WAREHOUSE_DAMAGE">Warehouse Damage</option>
                            <option value="RTV_DISPATCH">Supplier RMA</option>
                            <option value="RETURN_DAMAGED">Damaged Return</option>
                            <option value="ALLOCATION">Allocation</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Sort</label>
                        <select name="sort" id="sortFilter" class="form-select">
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="resetBtn" class="btn btn-soft-secondary w-100">Reset</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="tableContainer" class="card-body p-0">
            @include('admin.inventory.ledger.partials.table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const tableContainer = $('#tableContainer');
        const filterForm = $('#filterForm');

        function fetchLedger(url = "{{ route('admin.inventory.ledger.index') }}") {
            tableContainer.css('opacity', 0.5);
            const params = filterForm.serialize();
            const finalUrl = url + (url.includes('?') ? '&' : '?') + params;

            $.ajax({
                url: url,
                data: filterForm.serialize(),
                success: function(response) {
                    tableContainer.html(response).css('opacity', 1);
                    window.history.pushState({}, '', finalUrl);
                }
            });
        }

        let debounceTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchLedger, 500);
        });

        $('#warehouseFilter, #typeFilter, #sortFilter').on('change', function() {
            fetchLedger();
        });

        $('#resetBtn').on('click', function() {
            filterForm[0].reset();
            $('.select2').val('all').trigger('change');
            fetchLedger();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchLedger($(this).attr('href'));
        });
    });
</script>
@endsection
