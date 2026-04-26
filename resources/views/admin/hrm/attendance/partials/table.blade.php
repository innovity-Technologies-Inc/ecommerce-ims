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
<script>
    $(document).ready(function() {
        // Hide everything
        $('body > *').hide();
        
        // Create a print container
        const printContainer = $('<div class="print-container"></div>').appendTo('body');
        
        // Add business header
        const bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        const empName = '{{ request("search") }}' || 'All Employees';
        const dateRange = '{{ request("start_date") ?? "All Time" }} to {{ request("end_date") ?? "Present" }}';
        const generatedAt = new Date().toLocaleString();
        
        printContainer.append(`
            <div class="text-center mb-4 border-bottom pb-3">
                <h1 style="font-weight: bold; margin-bottom: 5px;">${bName}</h1>
                <h3 style="margin-bottom: 10px;">Attendance Report</h3>
                <p style="margin: 0; color: #666;">Employee: ${empName} | Period: ${dateRange}</p>
                <p style="margin: 0; color: #666; font-size: 11px;">Generated: ${generatedAt}</p>
            </div>
        `);

        // Clone the table and clean it
        const tableClone = $('.table-responsive').clone();
        tableClone.find('.no-print, .btn-group, .btn, iconify-icon').remove();
        
        printContainer.append(tableClone);

        // Apply print-specific styles
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                @media print {
                    @page { size: auto; margin: 1.5cm; }
                    body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; display: block !important; }
                    .print-container { display: block !important; width: 100% !important; }
                    table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; }
                    th, td { border: 1px solid #000 !important; padding: 8px 5px !important; font-size: 10px !important; color: black !important; text-align: center !important; }
                    th { background-color: #f8f9fa !important; font-weight: bold !important; -webkit-print-color-adjust: exact; }
                    .badge { border: 1px solid #000; padding: 2px 4px; border-radius: 3px; font-size: 9px; color: black !important; background: transparent !important; }
                    .d-flex { display: flex !important; }
                    .align-items-center { align-items: center !important; }
                    .gap-2 { gap: 0.5rem !important; }
                    img { max-width: 30px !important; height: auto !important; }
                }
            `)
            .appendTo('head');

        setTimeout(() => {
            window.print();
            if (confirm('Close this print tab?')) window.close();
        }, 800);
    });
</script>
@endif
