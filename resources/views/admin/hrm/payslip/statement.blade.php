<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Statement - {{ $payslip->payslip_number }}</title>
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/bootstrap.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; color: #111; }
        .statement-container { max-width: 900px; margin: 30px auto; }
        
        .payslip-box { 
            background: #fff; 
            padding: 50px; 
            border: 1px solid #ddd; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .header-section { border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 28px; font-weight: 800; color: #10b981; letter-spacing: -0.5px; }
        .statement-label { background: #10b981; color: #fff; padding: 5px 15px; font-weight: 700; text-transform: uppercase; font-size: 14px; border-radius: 4px; }
        
        .section-title { font-size: 16px; font-weight: 700; text-transform: uppercase; color: #10b981; margin-bottom: 15px; border-left: 4px solid #10b981; padding-left: 10px; }
        
        .info-list { list-style: none; padding: 0; margin-bottom: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px 40px; }
        .info-list li { font-size: 14px; margin-bottom: 5px; }
        .info-list li strong { font-weight: 600; color: #4b5563; }

        .breakdown-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1px solid #e5e7eb; }
        .breakdown-table th { background: #f9fafb; padding: 12px 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; border: 1px solid #e5e7eb; text-align: left; }
        .breakdown-table td { padding: 10px 15px; font-size: 13px; border: 1px solid #e5e7eb; vertical-align: middle; }
        .bg-light-gray { background-color: #f8fafc; font-weight: 700; }

        .summary-section { margin-bottom: 30px; }
        .summary-item { font-size: 14px; margin-bottom: 8px; }
        .summary-item strong { color: #374151; }

        .ack-box { background: #f8fafc; border: 1px dashed #cbd5e1; padding: 20px; font-size: 13px; line-height: 1.6; color: #334155; margin-bottom: 40px; border-radius: 6px; }

        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 100px; margin-top: 60px; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1px solid #333; margin-bottom: 10px; }
        .sig-text { font-size: 12px; font-weight: 700; }

        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: #fff !important; margin: 0; padding: 0; }
            .statement-container { margin: 0; max-width: 100% !important; }
            .payslip-box { box-shadow: none !important; border: 1px solid #eee !important; padding: 40px !important; }
            .no-print { display: none !important; }
            .section-title, .header-section { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

@php 
    $gs = \App\HelperClass::generalSettings(); 
    $admin = $payslip->admin;
    $netSalary = $payslip->net_salary;
    
    // Calculation Logic (Based on requested breakdown)
    // Basic: 60%, House Rent: 20%, Medical: 10%, Conveyance: 10%
    $basic = $netSalary * 0.60;
    $houseRent = $netSalary * 0.20;
    $medical = $netSalary * 0.10;
    $conveyance = $netSalary * 0.10;
@endphp

<div class="statement-container">
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold">Print Statement</button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 py-2 ms-2">Close Window</button>
    </div>

    <div class="payslip-box">
        <div class="header-section d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                @if($gs->light_logo)
                    <img src="{{ asset('storage/' . $gs->light_logo) }}" alt="logo" style="max-height: 60px;">
                @endif
                <div class="company-name text-uppercase">{{ $gs->business_name ?? 'Smart Ecom' }}</div>
            </div>
            <div class="text-end">
                <div class="statement-label mb-2">Salary Statement</div>
                <div class="fw-bold text-dark">#{{ $payslip->payslip_number }}</div>
            </div>
        </div>

        <div class="section-title">General Information</div>
        <ul class="info-list">
            <li><strong>Employee Name:</strong> {{ $admin->name }}</li>
            <li><strong>Employee ID:</strong> {{ $admin->employee_id ?? 'SE-' . str_pad($admin->id, 3, '0', STR_PAD_LEFT) }}</li>
            <li><strong>Designation:</strong> {{ $admin->designation ?? 'N/A' }}</li>
            <li><strong>Pay Period:</strong> {{ $payslip->start_date->format('F Y') }}</li>
            <li><strong>Payment Date:</strong> {{ $payslip->payment_date ? $payslip->payment_date->format('F d, Y') : date('F d, Y') }}</li>
            <li><strong>Payment Mode:</strong> {{ $payslip->payment_mode ?? 'Cash' }}</li>
        </ul>

        <div class="section-title">Salary Explanation</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Rate</th>
                    <th class="text-center">Total Hours</th>
                    <th class="text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Work Salary (Hourly Basis)</td>
                    <td class="text-center">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->salary_amount, 2) }}/hr</td>
                    <td class="text-center">{{ number_format($payslip->total_hours, 2) }} hrs</td>
                    <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</td>
                </tr>
                <tr class="bg-light-gray">
                    <td colspan="3" class="text-end">Gross Earnings</td>
                    <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Net Pay Summary</div>
        <div class="summary-section">
            <div class="summary-item"><strong>• Total Gross Pay:</strong> {{ number_format($netSalary, 2) }} {{ $gs->currency ?? 'BDT' }}</div>
            <div class="summary-item"><strong>• Total Deductions:</strong> 0.00 {{ $gs->currency ?? 'BDT' }}</div>
            <div class="summary-item"><strong>• Net Salary Payable:</strong> {{ number_format($netSalary, 2) }} {{ $gs->currency ?? 'BDT' }}</div>
            <div class="summary-item"><strong>• Amount in Words:</strong> {{ \App\HelperClass::numberToWords($netSalary) }}</div>
        </div>

        <div class="section-title">Acknowledgment of Receipt</div>
        <div class="ack-box">
            I, <strong>{{ $admin->name }}</strong>, hereby confirm that I have received the total net amount of <strong>{{ number_format($netSalary, 2) }} {{ $gs->currency ?? 'BDT' }}</strong> ({{ \App\HelperClass::numberToWords($netSalary) }}) for the month of <strong>{{ $payslip->start_date->format('F Y') }}</strong>. I acknowledge that this amount represents the full and final settlement of my salary and allowances for the specified period, and I have no further claims regarding this payment.
        </div>

        <div class="signature-grid">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-text">Employer Signature</div>
                <div class="text-muted small">Date: {{ $payslip->payment_date ? $payslip->payment_date->format('F d, Y') : date('F d, Y') }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-text">Employee Signature</div>
                <div class="text-muted small">(Acknowledged with thanks) Date: {{ date('F d, Y') }}</div>
            </div>
        </div>

        <div class="footer-note text-center mt-5 text-muted small" style="font-size: 10px; border-top: 1px dashed #ddd; pt-3;">
            This is a system-generated salary advice. {{ $gs->business_name ?? 'Smart Ecom' }}
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        if (!new URLSearchParams(window.location.search).has('no_auto_print')) {
            // Set empty title for print header to remove browser-added text
            document.title = " ";
            setTimeout(function() {
                window.print();
            }, 500);
        }
    }
</script>
</body>
</html>
