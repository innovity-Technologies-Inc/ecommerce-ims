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
        $('.no-print, .btn-group, .btn, iconify-icon, .card-header, .card-footer, .pagination, .dropdown, .search-bar, .topbar, .left-side-menu, .footer').attr('style', 'display:none !important');
        
        if (!$('.print-header').length) {
            $('<div class="print-header text-center mb-4">' +
                '<h2 style="margin-bottom:5px;">{{ \App\HelperClass::generalSettings()->business_name ?? "Smart Ecom" }}</h2>' +
                '<h3 style="margin-bottom:10px;">Payslip Generations Report</h3>' +
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
