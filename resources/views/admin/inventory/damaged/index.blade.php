@extends('admin.structure.app')

@section('title', 'Damaged Products Report')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Damaged Products Report</h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <form id="filterForm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search Product, Variant or Batch...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="sort" id="sortFilter" class="form-select">
                            <option value="batch_number">Batch Number (A-Z)</option>
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                            <option value="stock_low">Quantity: Low to High</option>
                            <option value="stock_high">Quantity: High to Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="resetBtn" class="btn btn-light w-100">Reset</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="tableContainer" class="card-body p-0">
            @include('admin.inventory.damaged.partials.table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const tableContainer = $('#tableContainer');
        const filterForm = $('#filterForm');

        function fetchStock(url = "{{ route('admin.inventory.damaged.index') }}") {
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
            debounceTimer = setTimeout(fetchStock, 500);
        });

        $('#sortFilter').on('change', function() {
            fetchStock();
        });

        $('#resetBtn').on('click', function() {
            filterForm[0].reset();
            fetchStock();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchStock($(this).attr('href'));
        });
    });
</script>
@endsection
