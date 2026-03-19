@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Best Selling Products</h4>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Time Period</label>
                    <select class="form-select filter-select" id="period-select">
                        <option value="all_time" {{ request('period') == 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>This Month</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>This Year</option>
                    </select>
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
    const tableContainer = $('#table-container');

    function fetchBestSellers() {
        const period = $('#period-select').val();

        tableContainer.css('opacity', '0.5');

        $.ajax({
            url: "{{ route('admin.products.best-selling') }}",
            type: 'GET',
            data: {
                period: period
            },
            success: function(response) {
                tableContainer.html(response);
                tableContainer.css('opacity', '1');

                const url = new URL(window.location.href);
                if (period) url.searchParams.set('period', period); else url.searchParams.delete('period');
                window.history.pushState({}, '', url);
            },
            error: function() {
                tableContainer.css('opacity', '1');
                toastr.error('Failed to fetch best sellers');
            }
        });
    }

    $('.filter-select').on('change', fetchBestSellers);

    $('#reset-filters').on('click', function() {
        $('#period-select').val('all_time');
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
