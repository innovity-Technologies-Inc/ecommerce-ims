@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Best Selling Products</h4>
        <button type="button" class="btn btn-sm btn-soft-secondary no-print" onclick="printFullReport()">
            <i class="bx bx-printer"></i> Print Full Report
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header no-print">
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="search-input" placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Time Period</label>
                    <select class="form-select filter-select" id="period-select">
                        <option value="all_time" {{ request('period') == 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>This Month</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Category</label>
                    <select class="form-select filter-select" id="category-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Brand</label>
                    <select class="form-select filter-select" id="brand-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Status</label>
                    <select class="form-select filter-select" id="status-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                
                <div class="col-lg-3 date-range-group" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control filter-select" id="date-from" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-3 date-range-group" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <label class="form-label">Date To</label>
                    <input type="date" class="form-control filter-select" id="date-to" value="{{ request('date_to') }}">
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0" id="table-container">
            @include('admin.products.partials.best_selling_table')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-print logic for full data view
    if (new URLSearchParams(window.location.search).has('is_print')) {
        $('.no-print, .btn-group, .btn, .bx, iconify-icon, .card-header, .card-footer, .pagination').hide();
        
        const bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        const dateStr = new Date().toLocaleString();
        const reportTitle = "Best Selling Products Report";
        
        $('body').prepend(`
            <div class="text-center mb-4 border-bottom pb-3">
                <h1>${bName}</h1>
                <h3>${reportTitle}</h3>
                <p>Generated: ${dateStr}</p>
            </div>
        `);

        $('<style>')
            .prop('type', 'text/css')
            .html('body{background:white !important; color:black !important; padding: 20px !important;} table{width:100% !important; border-collapse:collapse !important;} th,td{border:1px solid #ddd !important; padding:8px !important; font-size:12px !important;} .card{border:none !important; shadow:none !important;}')
            .appendTo('head');

        window.print();
    }

    function printFullReport() {
        const url = new URL(window.location.href);
        url.searchParams.set('is_print', '1');
        window.open(url.toString(), '_blank');
    }

    let searchTimer;
    const tableContainer = $('#table-container');

    function fetchBestSellers() {
        const search = $('#search-input').val();
        const period = $('#period-select').val();
        const categoryId = $('#category-select').val();
        const brandId = $('#brand-select').val();
        const status = $('#status-select').val();
        const dateFrom = $('#date-from').val();
        const dateTo = $('#date-to').val();

        // Toggle date range visibility
        if (period === 'custom') {
            $('.date-range-group').show();
        } else {
            $('.date-range-group').hide();
        }

        tableContainer.css('opacity', '0.5');

        $.ajax({
            url: "{{ route('admin.products.best-selling') }}",
            type: 'GET',
            data: {
                search: search,
                period: period,
                category_id: categoryId,
                brand_id: brandId,
                status: status,
                date_from: dateFrom,
                date_to: dateTo
            },
            success: function(response) {
                tableContainer.html(response);
                tableContainer.css('opacity', '1');

                const url = new URL(window.location.href);
                if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                if (period) url.searchParams.set('period', period); else url.searchParams.delete('period');
                if (categoryId) url.searchParams.set('category_id', categoryId); else url.searchParams.delete('category_id');
                if (brandId) url.searchParams.set('brand_id', brandId); else url.searchParams.delete('brand_id');
                if (status !== '') url.searchParams.set('status', status); else url.searchParams.delete('status');
                if (dateFrom) url.searchParams.set('date_from', dateFrom); else url.searchParams.delete('date_from');
                if (dateTo) url.searchParams.set('date_to', dateTo); else url.searchParams.delete('date_to');
                
                window.history.pushState({}, '', url);
            },
            error: function() {
                tableContainer.css('opacity', '1');
                toastr.error('Failed to fetch best sellers');
            }
        });
    }

    $('#search-input').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(fetchBestSellers, 500);
    });

    $('.filter-select').on('change', fetchBestSellers);

    $('#reset-filters').on('click', function() {
        $('#search-input').val('');
        $('#period-select').val('all_time');
        $('#category-select').val('');
        $('#brand-select').val('');
        $('#status-select').val('');
        $('#date-from').val('');
        $('#date-to').val('');
        fetchBestSellers();
    });

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
