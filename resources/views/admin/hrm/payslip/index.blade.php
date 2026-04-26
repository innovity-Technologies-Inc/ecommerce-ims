@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Payslip Management</h4>
            <p class="text-muted mb-0">Manage employee salaries and generate payslips.</p>
        </div>
        @can('hrm.edit')
        <a href="{{ route('admin.hrm.payslip.create') }}" class="btn btn-primary">
            <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon> Generate Payslip
        </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <form id="filter-form" class="row g-3">
                <div class="col-lg-2">
                    <div class="search-bar">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="role_id" id="roleFilter" class="form-select select2">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="status" id="statusFilter" class="form-select select2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="sort" id="sortFilter" class="form-select select2">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z to A</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="start_date" id="startDateFilter" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="end_date" id="endDateFilter" class="form-control" value="{{ request('end_date') }}">
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
        $('#roleFilter, #statusFilter, #sortFilter, #startDateFilter, #endDateFilter').on('change', function() {
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
</script>
@endsection
