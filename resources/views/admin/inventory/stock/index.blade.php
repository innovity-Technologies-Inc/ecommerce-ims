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
    /* Disable transitions and smooth scroll to prevent scroll anchoring conflicts in Firefox */
    .page-content, .wrapper {
        transition: none !important;
        -webkit-transition: none !important;
    }
    
    html {
        scroll-behavior: auto !important;
    }

    /* Force GPU acceleration on the main container to prevent repaint flickering */
    .content-page {
        will-change: transform;
        backface-visibility: hidden;
    }

    #tableContainer {
        min-height: 600px;
        overflow-anchor: none;
        /* Ensure the container doesn't have any transform that might trigger Firefox bugs */
        transform: none !important;
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
            // Lock current height and prevent scroll position jumping
            const currentHeight = tableContainer.outerHeight();
            tableContainer.css('min-height', currentHeight + 'px');
            
            // Record scroll position before update
            const scrollPos = $(window).scrollTop();

            loadingOverlay.removeClass('d-none');

            const params = filterForm.serialize();
            const finalUrl = url + (url.includes('?') ? '&' : '?') + params;

            $.ajax({
                url: url,
                data: filterForm.serialize(),
                success: function(response) {
                    tableContent.html(response);
                    
                    // Use requestAnimationFrame to ensure the DOM update is painted 
                    // before we release the height lock and restore scroll
                    requestAnimationFrame(() => {
                        loadingOverlay.addClass('d-none');
                        
                        // Restore scroll position to prevent jumps
                        $(window).scrollTop(scrollPos);
                        
                        // Set a stable baseline but allow content to expand
                        setTimeout(() => {
                            tableContainer.css('min-height', '600px');
                        }, 100);
                    });
                    
                    window.history.pushState({}, '', finalUrl);
                },
                error: function() {
                    loadingOverlay.addClass('d-none');
                    tableContainer.css('min-height', '600px');
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
