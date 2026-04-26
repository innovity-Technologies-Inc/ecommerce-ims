<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-accent text-dark">
            <tr>
                <th>SL</th>
                <th>Employee</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Total Time</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($attendances); @endphp
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($attendance->admin->image)
                                <img src="{{ asset('storage/' . $attendance->admin->image) }}" alt="" class="avatar-xs rounded-circle">
                            @else
                                <div class="avatar-xs d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle">
                                    {{ substr($attendance->admin->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="fw-medium">{{ $attendance->admin->name }}</span>
                        </div>
                    </td>
                    <td>{{ $attendance->date->format('d M, Y') }}</td>
                    <td><span class="badge bg-soft-success text-success">{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : '--:--' }}</span></td>
                    <td><span class="badge bg-soft-danger text-danger">{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : '--:--' }}</span></td>
                    <td>
                        @php
                            $hours = floor($attendance->total_minutes / 60);
                            $minutes = $attendance->total_minutes % 60;
                        @endphp
                        {{ $hours }}h {{ $minutes }}m
                    </td>
                    <td>
                        @if($attendance->is_manual)
                            <span class="badge bg-info">Manual</span>
                        @else
                            <span class="badge bg-primary">Auto</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 no-print">
    {{ $attendances->appends(request()->all())->links() }}
</div>

@if(request()->has('is_print'))
<style>
    @media print {
        .no-print, .btn-group, .btn, iconify-icon, .card-header, .card-footer, .pagination, .dropdown, .search-bar, .topbar, .main-nav, .footer, .left-side-menu, .header-title, .breadcrumb, .navbar-header, .navbar-custom { display: none !important; }
        body { background: white !important; margin: 0 !important; padding: 0 !important; }
        .page-content { margin: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    }
</style>
<script>
    window.onload = function() {
        // Double check hiding via JS for non-@media browsers
        $('.no-print, .btn-group, .btn, iconify-icon, .card-header, .card-footer, .pagination, .dropdown, .search-bar, .topbar, .left-side-menu, .footer').attr('style', 'display:none !important');
        
        // Add printable header if not exists
        if (!$('.print-header').length) {
            let employeeName = '{{ request("search") }}' || 'All Employees';
            $('<div class="print-header text-center mb-4">' +
                '<h2 style="margin-bottom:5px;">{{ \App\HelperClass::generalSettings()->business_name ?? "Smart Ecom" }}</h2>' +
                '<h3 style="margin-bottom:10px;">Attendance Report</h3>' +
                '<p style="margin:2px;">Employee: ' + employeeName + '</p>' +
                '<p style="margin:2px;">Period: {{ request("start_date") ?? "All Time" }} to {{ request("end_date") ?? "Present" }}</p>' +
                '<hr style="margin:20px 0;">' +
              '</div>').prependTo('.table-responsive');
        }

        setTimeout(function() {
            window.print();
        }, 500);

        window.onafterprint = function() {
            window.close();
        };
    };
</script>
@endif
