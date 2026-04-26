@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Generation Details: {{ $generation->title }}</h4>
            <p class="text-muted mb-0">Period: {{ $generation->start_date->format('d M, Y') }} - {{ $generation->end_date->format('d M, Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.hrm.payslip.batch.export', $generation->id) }}" class="btn btn-soft-success d-flex align-items-center gap-1">
                <iconify-icon icon="solar:export-bold-duotone"></iconify-icon> Export Excel
            </a>
            <button type="button" class="btn btn-soft-secondary d-flex align-items-center gap-1" onclick="printBatchReport()">
                <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> Print
            </button>
            <a href="{{ route('admin.hrm.payslip.index') }}" class="btn btn-secondary d-flex align-items-center gap-1">
                <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon> Back to List
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-md bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                        <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Employees</h6>
                        <h4 class="mb-0">{{ $generation->total_employees }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-md bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center me-3">
                        <iconify-icon icon="solar:wad-of-money-bold-duotone" class="fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Net Payout</h6>
                        <h4 class="mb-0">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($generation->total_amount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-md bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center me-3">
                        <iconify-icon icon="solar:calendar-bold-duotone" class="fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Generated Date</h6>
                        <h4 class="mb-0">{{ $generation->created_at->format('d M, Y') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Employee Payslips</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-centered">
                    <thead class="bg-light">
                        <tr>
                            <th>SL</th>
                            <th>Payslip #</th>
                            <th>Employee</th>
                            <th>Work Hours</th>
                            <th>Hourly Rate</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($generation->payslips as $index => $payslip)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $payslip->payslip_number }}</code></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($payslip->admin->image)
                                            <img src="{{ asset('storage/' . $payslip->admin->image) }}" alt="" class="avatar-xs rounded-circle">
                                        @else
                                            <div class="avatar-xs d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle">
                                                {{ substr($payslip->admin->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span class="fw-medium">{{ $payslip->admin->name }}</span>
                                    </div>
                                </td>
                                <td>{{ number_format($payslip->total_hours, 2) }} hrs</td>
                                <td>{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->salary_amount, 2) }}/hr</td>
                                <td class="fw-bold">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($payslip->status) {
                                            'paid' => 'bg-soft-success text-success',
                                            'cancelled' => 'bg-soft-danger text-danger',
                                            default => 'bg-soft-warning text-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($payslip->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.hrm.payslip.statement', $payslip->id) }}" target="_blank" class="btn btn-soft-primary btn-sm" title="Salary Statement">
                                            <iconify-icon icon="solar:printer-minimalistic-bold-duotone"></iconify-icon>
                                        </a>
                                        <button type="button" class="btn btn-soft-info btn-sm" data-bs-toggle="modal" data-bs-target="#updateStatus{{ $payslip->id }}">
                                            <iconify-icon icon="solar:pen-new-square-bold-duotone"></iconify-icon>
                                        </button>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="updateStatus{{ $payslip->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Status: {{ $payslip->payslip_number }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.hrm.payslip.update-status', $payslip->id) }}" method="POST">
                                                    @method('PUT')
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="pending" {{ $payslip->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="paid" {{ $payslip->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                                <option value="cancelled" {{ $payslip->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Payment Date</label>
                                                            <input type="date" name="payment_date" class="form-control" value="{{ $payslip->payment_date ? $payslip->payment_date->toDateString() : date('Y-m-d') }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function printBatchReport() {
        const url = new URL(window.location.href);
        url.searchParams.set('is_print', '1');
        window.open(url.href, '_blank');
    }

    $(document).ready(function() {
        if (new URLSearchParams(window.location.search).has('is_print')) {
            // Hide everything
            $('body > *').hide();
            
            // Create a print container
            const printContainer = $('<div class="print-container"></div>').appendTo('body');
            
            // Add business header
            const gs = {
                business_name: "{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}",
                light_logo: "{{ \App\HelperClass::generalSettings()->light_logo ? asset('storage/' . \App\HelperClass::generalSettings()->light_logo) : '' }}"
            };
            const generatedAt = new Date().toLocaleString();
            
            let headerHtml = `
                <div class="text-center mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        ${gs.light_logo ? `<img src="${gs.light_logo}" alt="logo" style="max-height: 50px;">` : ''}
                        <h1 style="font-weight: bold; margin: 0;">${gs.business_name}</h1>
                    </div>
                    <h3 style="margin-bottom: 10px;">Payslip Generation Batch Details</h3>
                    <p style="margin: 0; color: #666;">Batch: {{ $generation->title }}</p>
                    <p style="margin: 0; color: #666;">Period: {{ $generation->start_date->format("d M, Y") }} - {{ $generation->end_date->format("d M, Y") }}</p>
                    <p style="margin: 0; color: #666; font-size: 11px;">Generated: ${generatedAt}</p>
                </div>
            `;
            printContainer.append(headerHtml);

            // Clone the table and clean it
            const tableClone = $('.table-responsive').clone();
            tableClone.find('.no-print, .btn-group, .btn, iconify-icon, .modal').remove();
            
            printContainer.append(tableClone);

            // Apply print-specific styles
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @media print {
                        @page { size: auto; margin: 1.0cm; }
                        body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; display: block !important; }
                        .print-container { display: block !important; width: 100% !important; padding: 20px !important; }
                        table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; }
                        th, td { border: 1px solid #000 !important; padding: 8px 5px !important; font-size: 10px !important; color: black !important; text-align: center !important; }
                        th { background-color: #f8f9fa !important; font-weight: bold !important; -webkit-print-color-adjust: exact; }
                        .badge { border: 1px solid #000; padding: 2px 4px; border-radius: 3px; font-size: 9px; color: black !important; background: transparent !important; }
                        .fw-bold { font-weight: bold !important; }
                        .d-flex { display: flex !important; }
                        .align-items-center { align-items: center !important; }
                        .justify-content-center { justify-content: center !important; }
                        .gap-3 { gap: 1rem !important; }
                        img { max-height: 50px !important; width: auto !important; }
                    }
                `)
                .appendTo('head');

            // Set clean title for print header
            document.title = "Report";

            setTimeout(() => {
                window.print();
                if (confirm('Close this print tab?')) window.close();
            }, 500);
        }
    });
</script>
@endsection
