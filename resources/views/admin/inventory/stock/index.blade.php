@extends('admin.structure.app')

@section('title', 'Stock Trace')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Stock Trace</h4>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <form id="filterForm">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search Product, Variant or Batch...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="warehouse_id" id="warehouseFilter" class="form-select select2">
                            <option value="all">All Warehouses</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" id="sortFilter" class="form-select">
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                            <option value="stock_low">Stock: Low to High</option>
                            <option value="stock_high">Stock: High to Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="resetBtn" class="btn btn-light w-100">Reset</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="tableContainer" class="card-body p-0 position-relative" style="min-height: 500px; overflow-anchor: none;">
            <div id="loadingOverlay" class="position-absolute top-0 start-0 end-0 bottom-0 d-none d-flex align-items-center justify-content-center" style="z-index: 10; background: rgba(255,255,255,0.7); backdrop-filter: blur(1px);">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div id="tableContent">
                @include('admin.inventory.stock.partials.table')
            </div>
        </div>
    </div>
</div>

<style>
    /* 
       Firefox Stability Fix: 
       Disable scroll anchoring and fixed backgrounds which cause flickering 
       when scrolling to the bottom of pages with dynamic content.
    */
    html, body, .wrapper, .page-content, .content-page, #tableContainer {
        overflow-anchor: none !important;
    }
    
    .page-content {
        transition: none !important;
        -webkit-transition: none !important;
    }

    /* Disable the fixed background gradient only on this page to prevent repaint flickering */
    .content-page::before {
        display: none !important;
    }
    
    .content-page {
        background: var(--bs-body-bg) !important;
    }

    #tableContainer {
        min-height: 600px;
        /* Strict containment prevents Firefox from recalculating the whole page on table updates */
        contain: size layout style;
        background: #fff;
    }

    /* Remove heavy filters for better performance during scroll */
    #loadingOverlay {
        background: rgba(255,255,255,0.8) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const tableContainer = $('#tableContainer');
        const tableContent = $('#tableContent');
        const loadingOverlay = $('#loadingOverlay');
        const filterForm = $('#filterForm');

        function fetchStock(url = "{{ route('admin.inventory.stock.index') }}") {
            // Record scroll position
            const scrollPos = $(window).scrollTop();
            
            loadingOverlay.removeClass('d-none');

            const params = filterForm.serialize();
            const finalUrl = url + (url.includes('?') ? '&' : '?') + params;

            $.ajax({
                url: url,
                data: filterForm.serialize(),
                success: function(response) {
                    tableContent.html(response);
                    
                    // Simple stabilization
                    setTimeout(() => {
                        loadingOverlay.addClass('d-none');
                        $(window).scrollTop(scrollPos);
                    }, 50);
                    
                    window.history.pushState({}, '', finalUrl);
                },
                error: function() {
                    loadingOverlay.addClass('d-none');
                }
            });
        }

        let debounceTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchStock, 500);
        });

        $('#warehouseFilter, #sortFilter').on('change', function() {
            fetchStock();
        });

        $('#resetBtn').on('click', function() {
            filterForm[0].reset();
            $('.select2').val('all').trigger('change');
            fetchStock();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchStock($(this).attr('href'));
        });
    });
</script>
@endsection
