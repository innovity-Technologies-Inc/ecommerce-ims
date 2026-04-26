<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Statement - {{ $payslip->payslip_number }}</title>
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/bootstrap.min.css') }}">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .payslip-wrapper { max-width: 800px; margin: 40px auto; background: #fff; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.05); border-radius: 8px; }
        .company-logo { font-size: 24px; font-weight: 800; color: #10b981; }
        .payslip-title { font-size: 20px; font-weight: 700; color: #333; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .info-label { color: #666; font-size: 13px; font-weight: 600; margin-bottom: 2px; }
        .info-value { color: #111; font-size: 15px; font-weight: 700; }
        .table-custom { margin-top: 30px; border: 1px solid #eee; }
        .table-custom th { background: #f8f9fa; color: #333; font-weight: 700; border-bottom: 2px solid #10b981; }
        .summary-row { background: #f0fdf4; border-top: 2px solid #10b981; }
        .status-badge { padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 700; }
        .status-paid { background: #dcfce7; color: #15803d; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        
        @media print {
            body { background: #fff; margin: 0; }
            .payslip-wrapper { box-shadow: none; margin: 0; width: 100%; max-width: 100%; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print mt-4 text-center">
        <button onclick="window.print()" class="btn btn-primary">Print Statement</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="payslip-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <div class="company-logo">{{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}</div>
                <div class="text-muted small">Payroll Management System</div>
            </div>
            <div class="text-end">
                <div class="payslip-title">Salary Statement</div>
                <div class="info-value">#{{ $payslip->payslip_number }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="info-label">EMPLOYEE DETAILS</div>
                <div class="info-value">{{ $payslip->admin->name }}</div>
                <div class="text-muted small">{{ $payslip->admin->email }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="info-label">PAYMENT PERIOD</div>
                <div class="info-value">{{ $payslip->start_date->format('d M, Y') }} - {{ $payslip->end_date->format('d M, Y') }}</div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-4">
                <div class="info-label">GENERATED DATE</div>
                <div class="info-value">{{ $payslip->created_at->format('d M, Y') }}</div>
            </div>
            <div class="col-4">
                <div class="info-label">PAYMENT STATUS</div>
                <div>
                    <span class="status-badge {{ $payslip->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ strtoupper($payslip->status) }}
                    </span>
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="info-label">PAYMENT DATE</div>
                <div class="info-value">{{ $payslip->payment_date ? $payslip->payment_date->format('d M, Y') : '---' }}</div>
            </div>
        </div>

        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>DESCRIPTION</th>
                    <th class="text-center">TOTAL HOURS</th>
                    <th class="text-end">RATE</th>
                    <th class="text-end">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Working Salary (Hourly Basis)</td>
                    <td class="text-center">{{ number_format($payslip->total_hours, 2) }} hrs</td>
                    <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->salary_amount, 2) }}/hr</td>
                    <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</td>
                </tr>
                <tr class="summary-row">
                    <td colspan="3" class="text-end fw-bold">NET SALARY PAYOUT</td>
                    <td class="text-end fw-bold text-success" style="font-size: 18px;">
                        {{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-5 pt-5">
            <div class="row">
                <div class="col-6">
                    <div style="width: 200px; border-top: 1px solid #ccc; padding-top: 10px;" class="text-center small">
                        Employee Signature
                    </div>
                </div>
                <div class="col-6 text-end d-flex justify-content-end">
                    <div style="width: 200px; border-top: 1px solid #ccc; padding-top: 10px;" class="text-center small">
                        Authorized Signature
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 text-muted small border-top pt-3">
            This is a computer-generated document and does not require a physical stamp.
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        if (!new URLSearchParams(window.location.search).has('no_auto_print')) {
            window.print();
        }
    }
</script>
</body>
</html>
