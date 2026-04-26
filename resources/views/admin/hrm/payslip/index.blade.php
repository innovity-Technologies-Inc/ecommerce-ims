@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Payslip Management</h4>
            <p class="text-muted mb-0">Manage employee salaries and bulk generate payslips.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-soft-success dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <iconify-icon icon="solar:export-bold-duotone"></iconify-icon> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.hrm.payslip.export', array_merge(request()->all(), ['type' => 'excel'])) }}">Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.hrm.payslip.export', array_merge(request()->all(), ['type' => 'csv'])) }}">CSV (.csv)</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-soft-secondary d-flex align-items-center gap-1" onclick="printFullReport()">
                <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> Print
            </button>
            @can('hrm.edit')
            <a href="{{ route('admin.hrm.payslip.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon> Generate New Batch
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <form id="filter-form" class="row g-3">
                <div class="col-lg-4">
                    <div class="search-bar">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search batch title..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="sort" id="sortFilter" class="form-select select2">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <input type="date" name="start_date" id="startDateFilter" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                </div>
                <div class="col-lg-3">
                    <input type="date" name="end_date" id="endDateFilter" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                </div>
            </form>
        </div>
        <div class="card-body" id="table-container">
            @include('admin.hrm.payslip.partials.table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // AJAX live search and filtering
        function fetchPayslips() {
            let url = "{{ route('admin.hrm.payslip.index') }}";
            let data = $('#filter-form').serialize();

            $.ajax({
                url: url,
                data: data,
                success: function(response) {
                    $('#table-container').html(response);
                    window.history.pushState({}, '', url + '?' + data);
                }
            });
        }

        // Debounce for search input
        let debounceTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchPayslips, 500);
        });

        // Immediate triggers for select and dates
        $('#sortFilter, #startDateFilter, #endDateFilter').on('change', function() {
            fetchPayslips();
        });

        // Pagination AJAX
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            
            $.ajax({
                url: url,
                success: function(response) {
                    $('#table-container').html(response);
                    window.history.pushState({}, '', url);
                }
            });
        });
    });

    function printFullReport() {
        const url = new URL(window.location.href);
        url.searchParams.set('is_print', '1');
        
        const printWin = window.open(url.href, '_blank');
        if (!printWin) {
            alert('Please allow popups to print reports.');
        }
    }
</script>
@endsection
