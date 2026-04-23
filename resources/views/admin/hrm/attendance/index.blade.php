@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Attendance Management</h4>
            <p class="text-muted mb-0">Track and manage employee work hours.</p>
        </div>
        @can('hrm.edit')
        <a href="{{ route('admin.hrm.attendance.create') }}" class="btn btn-primary">
            <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon> Record Attendance
        </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <form id="filter-form" class="row g-3">
                <div class="col-lg-3">
                    <div class="search-bar">
                        <input type="text" name="search" class="form-control" placeholder="Search employee..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="admin_id" class="form-control select2">
                        <option value="">All Employees</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date" value="{{ request('start_date') }}">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date" value="{{ request('end_date') }}">
                </div>
                <div class="col-lg-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('admin.hrm.attendance.index') }}" class="btn btn-soft-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body" id="table-container">
            @include('admin.hrm.attendance.partials.table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2();

        // AJAX Filtering
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            let url = "{{ route('admin.hrm.attendance.index') }}";
            let data = $(this).serialize();

            $.ajax({
                url: url,
                data: data,
                success: function(response) {
                    $('#table-container').html(response);
                    window.history.pushState({}, '', url + '?' + data);
                }
            });
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
