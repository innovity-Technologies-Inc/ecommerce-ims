<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-accent text-dark">
            <tr>
                <th>SL</th>
                <th>Generation Title</th>
                <th>Period</th>
                <th>Employees</th>
                <th>Total Amount</th>
                <th>Generated At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($generations); @endphp
            @forelse($generations as $generation)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td><span class="fw-bold">{{ $generation->title }}</span></td>
                    <td>{{ $generation->start_date->format('d M') }} - {{ $generation->end_date->format('d M, Y') }}</td>
                    <td><span class="badge bg-soft-info text-info">{{ $generation->total_employees }} Employees</span></td>
                    <td class="fw-bold">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($generation->total_amount, 2) }}</td>
                    <td>{{ $generation->created_at->format('d M, Y h:i A') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.hrm.payslip.show', $generation->id) }}" class="btn btn-soft-primary btn-sm">
                                <iconify-icon icon="solar:eye-broken"></iconify-icon>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No payslip generations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 no-print">
    {{ $generations->appends(request()->all())->links() }}
</div>

@if(request()->has('is_print'))
<script>
    $(document).ready(function() {
        $('.no-print, .btn-group, .btn, iconify-icon, .card-header, .card-footer, .pagination, .dropdown, .search-bar, .topbar, .main-nav, .footer').hide();
        $('body').css('background', 'white');
        $('.card').css('border', 'none').css('box-shadow', 'none');
        
        // Add printable header
        if (!$('.print-header').length) {
            $('<div class="print-header text-center mb-4">' +
                '<h2>{{ \App\HelperClass::generalSettings()->business_name ?? "Smart Ecom" }}</h2>' +
                '<h4>Payslip Generations Report</h4>' +
                '<p>Period: {{ request("start_date") ?? "All Time" }} to {{ request("end_date") ?? "Present" }}</p>' +
              '</div>').prependTo('.card-body');
        }

        window.print();
        setTimeout(function() {
            if (confirm('Close this print tab?')) window.close();
        }, 500);
    });
</script>
@endif
