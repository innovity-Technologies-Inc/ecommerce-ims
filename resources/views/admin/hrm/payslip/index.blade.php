@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Payslip Management</h4>
            <p class="text-muted mb-0">Manage employee salaries and bulk generate payslips.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.hrm.payslip.export', request()->all()) }}" class="btn btn-soft-success d-flex align-items-center gap-1">
                <iconify-icon icon="solar:export-bold-duotone"></iconify-icon> Export Excel
            </a>
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

        // Auto-print logic
        if (new URLSearchParams(window.location.search).has('is_print')) {
            // Hide everything
            $('body > *').hide();
            
            // Create a print container
            const printContainer = $('<div class="print-container"></div>').appendTo('body');
            
            // Add business header
            const gs = {
                business_name: "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}"
            };
            const dateRange = '{{ request("start_date") ?? "All Time" }} to {{ request("end_date") ?? "Present" }}';
            const generatedAt = new Date().toLocaleString();
            
            let headerHtml = `
                <div class="text-center mb-4 border-bottom pb-3">
                    <h1 style="font-weight: bold; margin-bottom: 10px;">${gs.business_name}</h1>
                    <h3 style="margin-bottom: 10px;">Payslip Generations Report</h3>
                    <p style="margin: 0; color: #666;">Period: ${dateRange}</p>
                    <p style="margin: 0; color: #666; font-size: 11px;">Generated: ${generatedAt}</p>
                </div>
            `;
            printContainer.append(headerHtml);

            // Clone the table and clean it
            const tableClone = $('.table-responsive').clone();
            
            // Remove Action column from the cloned table
            const actionHeaderIndex = tableClone.find('th:contains("Action")').index();
            if (actionHeaderIndex !== -1) {
                tableClone.find('tr').each(function() {
                    $(this).find('td, th').eq(actionHeaderIndex).remove();
                });
            }

            tableClone.find('.no-print, .btn-group, .btn, iconify-icon').remove();
            
            printContainer.append(tableClone);

            // Apply print-specific styles
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @media print {
                        @page { margin: 0; }
                        body { margin: 1.6cm !important; background: white !important; color: black !important; display: block !important; }
                        .print-container { display: block !important; width: 100% !important; padding: 20px !important; }
                        table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; }
                        th, td { border: 1px solid #000 !important; padding: 8px 5px !important; font-size: 10px !important; color: black !important; text-align: center !important; }
                        th { background-color: #f8f9fa !important; font-weight: bold !important; -webkit-print-color-adjust: exact; }
                        .badge { border: 1px solid #000; padding: 2px 4px; border-radius: 3px; font-size: 9px; color: black !important; background: transparent !important; }
                        .fw-bold { font-weight: bold !important; }
                    }
                `)
                .appendTo('head');

            // Set empty title to remove browser-added site name/URL from top/bottom
            document.title = " ";

            setTimeout(() => {
                window.print();
                if (confirm('Close this print tab?')) window.close();
            }, 500);
        }

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
