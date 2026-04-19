@extends('admin.structure.app')
@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Low Stock Alerts</h4>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
            <button type="button" class="btn btn-sm btn-soft-secondary no-print" onclick="printFullReport()">
                <i class="bx bx-printer"></i> Print Report
            </button>
        </div>
    </div>

    <div class="card mb-3 no-print">
        <div class="card-body">
            <form id="filterForm">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search product name or SKU...">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0 position-relative" id="table-container">
                    <div id="loadingSpinner" class="position-absolute top-50 start-50 translate-middle d-none" style="z-index: 10;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="tableContent">
                        @include('admin.products.partials.low_stock_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const tableContainer = $('#table-container');
    const tableContent = $('#tableContent');
    const loadingSpinner = $('#loadingSpinner');
    const filterForm = $('#filterForm');

    function fetchLowStock(url = "{{ route('admin.dashboard.low_stock') }}") {
        tableContainer.css('min-height', tableContainer.height() + 'px');
        tableContent.css('opacity', 0.5);
        loadingSpinner.removeClass('d-none');

        const params = filterForm.serialize();
        const finalUrl = url + (url.includes('?') ? '&' : '?') + params;

        $.ajax({
            url: url,
            data: filterForm.serialize(),
            success: function(response) {
                tableContent.html(response).css('opacity', 1);
                loadingSpinner.addClass('d-none');
                tableContainer.css('min-height', '');
                window.history.pushState({}, '', finalUrl);
            },
            error: function() {
                tableContent.css('opacity', 1);
                loadingSpinner.addClass('d-none');
                tableContainer.css('min-height', '');
            }
        });
    }

    let debounceTimer;
    $('#searchInput').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchLowStock, 500);
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        fetchLowStock($(this).attr('href'));
    });

    // Auto-print logic for full data view
    if (new URLSearchParams(window.location.search).has('is_print')) {
        $('.no-print, .btn-group, .btn, .bx, iconify-icon, .card-header, .card-footer, .pagination').hide();
        
        const bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        const dateStr = new Date().toLocaleString();
        const reportTitle = "Low Stock Alerts Report";
        
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
});

function printFullReport() {
    const url = new URL(window.location.href);
    url.searchParams.set('is_print', '1');
    url.searchParams.delete('page');
    window.open(url.toString(), '_blank');
}
</script>
@endsection
