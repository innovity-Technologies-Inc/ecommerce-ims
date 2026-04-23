@extends('admin.structure.app')

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3 no-print">
        <h4 class="mb-0">Payslip: {{ $payslip->payslip_number }}</h4>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success btn-sm">
                <iconify-icon icon="solar:printer-bold-duotone" class="me-1"></iconify-icon> Print
            </button>
            <a href="{{ route('admin.hrm.payslip.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card p-4 shadow-sm border-0" id="printableArea">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        @if(\App\HelperClass::generalSettings()->dark_logo)
                            <img src="{{ asset('storage/' . \App\HelperClass::generalSettings()->dark_logo) }}" alt="Logo" height="40">
                        @else
                            <h3 class="text-primary mb-0">{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}</h3>
                        @endif
                        <p class="text-muted mt-2 mb-0">Official Salary Statement</p>
                    </div>
                    <div class="text-end">
                        <h4 class="text-uppercase fw-bold mb-1">PAYSLIP</h4>
                        <p class="mb-0">#{{ $payslip->payslip_number }}</p>
                        <p class="mb-0">Date: {{ $payslip->created_at->format('d M, Y') }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Employee Details:</h6>
                        <h5 class="mb-1">{{ $payslip->admin->name }}</h5>
                        <p class="mb-0 text-muted">{{ $payslip->admin->email }}</p>
                        <p class="mb-0 text-muted">Role: {{ $payslip->admin->getRoleNames()->first() }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Pay Period:</h6>
                        <h5 class="mb-1">{{ $payslip->start_date->format('d M, Y') }} to {{ $payslip->end_date->format('d M, Y') }}</h5>
                        <p class="mb-0 text-muted">Status: 
                            <span class="badge {{ $payslip->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ strtoupper($payslip->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Salary Type</td>
                                <td class="text-end text-capitalize">{{ $payslip->salary_type }}</td>
                            </tr>
                            <tr>
                                <td>Base Salary Rate</td>
                                <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->salary_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Total Work Hours</td>
                                <td class="text-end">{{ number_format($payslip->total_hours, 2) }} hrs</td>
                            </tr>
                            <tr class="fw-bold bg-light">
                                <td class="py-3">NET SALARY PAYABLE</td>
                                <td class="text-end py-3 fs-5">
                                    {{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-5 pt-4">
                    <div class="col-6 text-center">
                        <div class="border-top pt-2 mx-auto" style="width: 150px;">
                            <p class="mb-0 fw-bold small">Employee Signature</p>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="border-top pt-2 mx-auto" style="width: 150px;">
                            <p class="mb-0 fw-bold small">Authorized Signature</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center text-muted small border-top pt-3">
                    <p class="mb-0">This is a computer-generated payslip and does not require a physical signature.</p>
                </div>
            </div>

            @if($payslip->status !== 'paid')
            <div class="card mt-3 no-print">
                <div class="card-body">
                    <h5 class="mb-3">Update Payment Status</h5>
                    <form action="{{ route('admin.hrm.payslip.update-status', $payslip->id) }}" method="POST" class="d-flex gap-3 align-items-end">
                        @csrf
                        @method('PUT')
                        <div class="flex-grow-1">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" {{ $payslip->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $payslip->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ $payslip->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
    }
    body {
        background: white !important;
    }
    .container-xxl {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
