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
        // Hide everything
        $('body > *').hide();
        
        // Create a print container
        const printContainer = $('<div class="print-container"></div>').appendTo('body');
        
        // Add business header
        const bName = "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}";
        const dateRange = '{{ request("start_date") ?? "All Time" }} to {{ request("end_date") ?? "Present" }}';
        const generatedAt = new Date().toLocaleString();
        
        printContainer.append(`
            <div class="text-center mb-4 border-bottom pb-3">
                <h1 style="font-weight: bold; margin-bottom: 5px;">${bName}</h1>
                <h3 style="margin-bottom: 10px;">Payslip Generations Report</h3>
                <p style="margin: 0; color: #666;">Period: ${dateRange}</p>
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
                    .fw-bold { font-weight: bold !important; }
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
