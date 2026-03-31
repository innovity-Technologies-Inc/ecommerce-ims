@extends('admin.structure.app')
@section('content')

<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Damaged Products (Wastage)</h4>
        @can('returns.edit')
        <a href="{{ route('admin.wastage.create') }}" class="btn btn-danger btn-sm">Record Warehouse Damage</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="search-input" placeholder="Search by Product Name or Reason..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" id="reset-filters">Reset Filters</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0" id="table-container">
            @include('admin.returns.partials.wastages_table')
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;
        const tableContainer = $('#table-container');

        function fetchWastages() {
            const search = $('#search-input').val();

            tableContainer.css('opacity', '0.5');

            $.ajax({
                url: "{{ route('admin.returns.wastages') }}",
                type: 'GET',
                data: {
                    search: search
                },
                success: function(response) {
                    tableContainer.html(response);
                    tableContainer.css('opacity', '1');

                    const url = new URL(window.location.href);
                    if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                    window.history.pushState({}, '', url);
                },
                error: function() {
                    tableContainer.css('opacity', '1');
                    toastr.error('Failed to fetch wastage records');
                }
            });
        }

        $('#search-input').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchWastages, 500);
        });

        $('#reset-filters').on('click', function() {
            $('#search-input').val('');
            fetchWastages();
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
