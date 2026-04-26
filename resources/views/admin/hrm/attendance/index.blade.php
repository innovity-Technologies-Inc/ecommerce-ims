@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Attendance Management</h4>
            <p class="text-muted mb-0">Track and manage employee work hours.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-soft-success dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <iconify-icon icon="solar:export-bold-duotone"></iconify-icon> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.hrm.attendance.export', array_merge(request()->all(), ['type' => 'excel'])) }}">Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.hrm.attendance.export', array_merge(request()->all(), ['type' => 'csv'])) }}">CSV (.csv)</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-soft-secondary d-flex align-items-center gap-1" onclick="printFullReport()">
                <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> Print
            </button>
            @can('hrm.edit')
            <a href="{{ route('admin.hrm.attendance.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon> Record Attendance
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <form id="filter-form" class="row g-3">
                <div class="col-lg-3">
                    <div class="search-bar">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search employee..." value="{{ request('search') }}">
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
                    <select name="sort" id="sortFilter" class="form-select select2">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z to A</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="start_date" id="startDateFilter" class="form-control" placeholder="Start Date" value="{{ request('start_date') }}">
                </div>
                <div class="col-lg-3">
                    <input type="date" name="end_date" id="endDateFilter" class="form-control" placeholder="End Date" value="{{ request('end_date') }}">
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
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // AJAX live search and filtering
        function fetchAttendance() {
            let url = "{{ route('admin.hrm.attendance.index') }}";
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
            debounceTimer = setTimeout(fetchAttendance, 500);
        });

        // Immediate triggers for select and dates
        $('#roleFilter, #sortFilter, #startDateFilter, #endDateFilter').on('change', function() {
            fetchAttendance();
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
