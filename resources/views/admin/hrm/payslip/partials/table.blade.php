<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-accent text-dark">
            <tr>
                <th>SL</th>
                <th>Payslip #</th>
                <th>Employee</th>
                <th>Period</th>
                <th>Work Hours</th>
                <th>Net Salary</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($payslips); @endphp
            @forelse($payslips as $payslip)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td><span class="fw-bold">{{ $payslip->payslip_number }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($payslip->admin->image)
                                <img src="{{ asset('storage/' . $payslip->admin->image) }}" alt="" class="avatar-xs rounded-circle">
                            @else
                                <div class="avatar-xs d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle">
                                    {{ substr($payslip->admin->name, 0, 1) }}
                                </div>
                            @endif
                            <span>{{ $payslip->admin->name }}</span>
                        </div>
                    </td>
                    <td>{{ date('F', mktime(0, 0, 0, $payslip->month, 1)) }} {{ $payslip->year }}</td>
                    <td>{{ number_format($payslip->total_hours, 2) }} hrs</td>
                    <td>{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</td>
                    <td>
                        @if($payslip->status === 'paid')
                            <span class="badge bg-soft-success text-success">Paid</span>
                        @elseif($payslip->status === 'cancelled')
                            <span class="badge bg-soft-danger text-danger">Cancelled</span>
                        @else
                            <span class="badge bg-soft-warning text-warning">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.hrm.payslip.show', $payslip->id) }}" class="btn btn-soft-primary btn-sm">
                                <iconify-icon icon="solar:eye-broken"></iconify-icon>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No payslips found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $payslips->links() }}
</div>
